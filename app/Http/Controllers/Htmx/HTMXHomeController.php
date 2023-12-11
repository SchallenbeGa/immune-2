<?php

namespace App\Http\Controllers\Htmx;

use App\Models\Tag;
use App\Models\Article;
use App\Models\Trade;
use App\Support\Helpers;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Ohlvc;
use App\Models\Symbol;
use App\Models\Signal;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class HTMXHomeController extends Controller
{
    public function index()
    {
        return view('home.partials.index')
            .view('components.navbar', [
                'navbar_active' => 'home'
            ]);
    }

    public function favorite(Article $article)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }
        
        $isArticleFavoritedByUser = $article->toggleUserFavorite(auth()->user());

        return view('home.partials.article-favorite-button', [
            'article' => $article,
            'favorite_count' => $article->favoritedUsers->count(),
            'is_favorited' => $isArticleFavoritedByUser,
        ]);
    }

    public function yourFeed()
    {
        $articles = Article::with(['user', 'tags', 'favoritedUsers']);

        $feedNavbarItems = Helpers::feedNavbarItems();
        $feedNavbarItems['personal']['is_active'] = true;

        $articles = $articles->ofAuthorsFollowedByUser(auth()->user())->paginate(5);

        return view('home.partials.post-preview', ['articles' => $articles])
            .view('home.partials.pagination', [
                'paginator' => $articles,
                'page_number' => request()->page ?? 1
            ])
            .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
            .view('components.htmx.head', [
                'page_title' => 'Your feed —'
            ]);
    }

    public function globalFeed()
    {
        // $articles = Article::with(['user', 'tags', 'favoritedUsers']);

        // $feedNavbarItems = Helpers::feedNavbarItems();
        // $feedNavbarItems['global']['is_active'] = true;

        // $articles = $articles->paginate(5);

        // return view('home.partials.post-preview', ['articles' => $articles])
        //     .view('home.partials.pagination', [
        //         'paginator' => $articles,
        //         'page_number' => request()->page ?? 1
        //     ])
        //     .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
        //     .view('components.htmx.head', [
        //         'page_title' => ''
        //     ]);
        $symb = Symbol::orderBy('updated_at','DESC')->limit(5)->get();
        $data = [];
        foreach($symb as $sy){
            $pnl = "";
            $sy->pnl = $pnl;
            $p = 0;
            $l =0;
            $all_trade = Trade::where('symbol_id',$sy->id)->orderBy('updated_at','DESC')->get();
            if($all_trade!=null && count($all_trade)>1){
                foreach($all_trade as $trade){
                    switch($trade->side){
                        case "buy":
                            $p += $trade->price*$trade->quantity;
                            break;
                        case "sell":
                            $l += $trade->price*$trade->quantity;
                            break;
                    }
                }
                $ol = ($sy->last_price)-($p-$l);
                $percent = round((float)$ol/100,4) . '%';
                $sy->pnl = $percent;
                
            }
            $last_candle = Ohlvc::where('symbol_id',$sy->id)->orderBy('updated_at','DESC')->first();
            if($last_candle!=null){
                $sy->last_price = $last_candle->close;
            }
            $last_trade = Trade::where('symbol_id',$sy->id)->orderBy('updated_at','DESC')->first();
            if($last_trade!=null){
                if($last_trade->side=="buy"){
                    $ol = ($sy->last_price)-$last_trade->price;
                    $percent = round((float)$ol/100,4) . '%';
                }else{
                    $last_sell = Trade::where('symbol_id',$sy->id)->orderBy('updated_at','DESC')->skip(1)->first()->price;
                    $ol = ($last_sell)-$last_trade->price;
                    $percent = round((float)$ol*100,4) . '%';
                }
                if($sy->pnl==""){
                    $sy->pnl  = $percent;
                }
                $last_trade = "Last trade : ".$last_trade->side." at ".$last_trade->price;
            }
            $sy->last_trade = $last_trade;
            $last_msg = Signal::where('symbol_id',$sy->id)->orderBy('updated_at','DESC')->first();
            if($last_msg!=null){
                $last_msg = $last_msg->msg;
            }
            $sy->last_msg = $last_msg;
            array_push($data,$sy);
        }
       
        
       // dd($symb);

    
        $feedNavbarItems = Helpers::feedNavbarItems();
        $feedNavbarItems['global']['is_active'] = true;
        
        // $ohlvc = $ohlvc->paginate(10);
        // return view('home.partials.ohlvc-preview', ['ohlvc' => $ohlvc])
        //     .view('home.partials.pagination', [
        //         'paginator' => $ohlvc,
        //         'page_number' => request()->page ?? 1
        //     ])
        //     .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
        //     .view('components.htmx.head', [
        //         'page_title' => ''
        //     ]);

        return view('home.partials.symbol-preview', ['symbol' => $data])
            .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
            .view('components.htmx.head', [
                'page_title' => ''
            ]);

    }

    public function tradeFeed()
    {
        // $articles = Article::with(['user', 'tags', 'favoritedUsers']);

        // $feedNavbarItems = Helpers::feedNavbarItems();
        // $feedNavbarItems['global']['is_active'] = true;

        // $articles = $articles->paginate(5);

        // return view('home.partials.post-preview', ['articles' => $articles])
        //     .view('home.partials.pagination', [
        //         'paginator' => $articles,
        //         'page_number' => request()->page ?? 1
        //     ])
        //     .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
        //     .view('components.htmx.head', [
        //         'page_title' => ''
        //     ]);
        $symb = Trade::orderBy('updated_at','DESC')->limit(5)->get();
        
        foreach($symb as $sy){
            $symbol = Symbol::where('id',$sy->symbol_id)->orderBy('updated_at','DESC')->first();
            if($symbol!=null){
                $sy->name = $symbol->name;
            }
        }
        $feedNavbarItems = Helpers::feedNavbarItems();
        $feedNavbarItems['trade']['is_active'] = true;
        
        // $ohlvc = $ohlvc->paginate(10);
        // return view('home.partials.ohlvc-preview', ['ohlvc' => $ohlvc])
        //     .view('home.partials.pagination', [
        //         'paginator' => $ohlvc,
        //         'page_number' => request()->page ?? 1
        //     ])
        //     .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
        //     .view('components.htmx.head', [
        //         'page_title' => ''
        //     ]);

        return view('home.partials.trade-preview', ['trades' => $symb])
            .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
            .view('components.htmx.head', [
                'page_title' => ''
            ]);

    }

    public function search(Request $request)
    {
        if($request->content==null){
            $symbols = Symbol::orderBy('updated_at', 'desc')->paginate(20);
            return view('home.partials.search-item-list', [
                'symbols' => $symbols
            ]);
        }
        $symbols = Symbol::Where('name','like',strtoupper($request->content)."%")->get();

        
        return view('home.partials.search-item-list', ['symbols' => $symbols]);
        
       
    }
    public function symbolList(){
        $symbols = Symbol::orderBy('updated_at', 'desc')->paginate(20);
        return view('home.partials.search-item-list', [
            'symbols' => $symbols
        ]);
    }
    public function tagFeed(Tag $tag)
    {
        $articles = Article::with(['tags', 'favoritedUsers'])
            ->whereHas('tags', function($q) use ($tag) {
                $q->where('id', $tag->id);
            })
            ->paginate(5);

        $feedNavbarItems = Helpers::feedNavbarItems();
        $feedNavbarItems['tag'] = [
            'title' => $tag->name,
            'is_active' => true,
            'hx_get_url' => '/htmx/tag-feed',
            'hx_push_url' => '/tag-feed/' . $tag->name
        ];

        return view('home.partials.post-preview', ['articles' => $articles])
            .view('home.partials.pagination', [
                'paginator' => $articles,
                'page_number' => request()->page ?? 1
            ])
            .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
            .view('components.htmx.head', [
                'page_title' => Str::words($tag->name, 40, '') . ' —'
            ]);
    }

    public function tagList()
    {
        $popularTags = Tag::favoriteTags();

        return view('home.partials.tag-item-list', [
            'popularTags' => $popularTags
        ]);
    }
}
