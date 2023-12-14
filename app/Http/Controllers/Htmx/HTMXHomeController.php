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
    public function favorite_symbol(Symbol $symbol)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }
        $isSymbolFavoritedByUser = $symbol->toggleUserFavorite(auth()->user());

        return view('home.partials.symbol-favorite-button', [
            'symbol' => $symbol,
            'favorite_count' => $symbol->favoritedUsers->count(),
            'is_favorited' => $isSymbolFavoritedByUser,
        ]);
    }

    public function yourFeed()
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }
        $symbols = auth()->user()->load(['favorites']);
        $feedNavbarItems = Helpers::feedNavbarItems();
        $feedNavbarItems['personal']['is_active'] = true;
        $so = $symbols->favorites;
        $symbols = $symbols->paginate(20);

        return view('home.partials.symbol-preview', ['symbol' => $so])
            .view('home.partials.pagination', [
                'paginator' => $symbols,
                'page_number' => request()->page ?? 1
            ])
            .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
            .view('components.htmx.head', [
                'page_title' => 'Your feed —'
            ]);
    }

    public function globalFeed()
    {
        $symb = Symbol::with(['favoritedUsers']);
        $sa = $symb;
        $so = $symb->paginate(20);
        $data = [];
        $fees = 0;
        $total_profit = 0;
        foreach($sa->get() as $sy){
            $pnl = "0.00 %";
            $sy->pnl = $pnl;
            $sy->profit = "nothing ah ah.";
            $p = 0;
            $l =0;
            $nb_p=[];
            $nb_s=[];
            $trade_quantity=[];
            $all_trade = Trade::where('symbol_id',$sy->id)->get();
            if($all_trade!=null && count($all_trade)>1){
                foreach($all_trade as $trade){
                    switch($trade->side){
                        case "buy":
                            $trade_quantity[] = 1000/$trade->price;
                            $nb_p[] = floatval($trade->price);
                            break;
                        case "sell":
                            if(count($nb_p)==0){
                                $nb_s[] = $trade->price-$nb_p[0]*$trade_quantity[0];
                            }else{
                                $nb_s[] = ($trade->price-$nb_p[count($nb_p)-1])*($trade_quantity[count($trade_quantity)-1]);
                            }
                            break;
                    }  
                }

                $p = 0;
                $l = 0;
                $nb_t = 0;
                for ($i = 0; $i < count($nb_s); $i++) {
                    $nb_t++;
                    $p+= $nb_p[$i];
                    $l+= $nb_s[$i];
                }
                $sy->nb_trade = ($nb_t+1);
                $ol = ($sy->last_price)-($p-$l);
                $percent = round((float)$ol/100,4) . '%';
                $sy->pnl = $percent;
                $sy->profit = $l;
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
            $total_profit+=$sy->profit;
            $sy->profit = (round($l,5)-(($l/100)*0.1000))."$";
            array_push($data,$sy);
        }

        $feedNavbarItems = Helpers::feedNavbarItems();
        $feedNavbarItems['global']['is_active'] = true;

        return view('home.partials.symbol-preview', ['symbol' => $data,'total'=>$total_profit])
            .view('home.partials.pagination', [
                'paginator' => $so,
                'page_number' => request()->page ?? 1
            ])
            .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
            .view('components.htmx.head', [
                'page_title' => ''
            ]);
    }

    public function tradeFeed()
    {
        //todo
        $symb = Trade::orderBy('updated_at','DESC')->limit(25);
        $sa = $symb;
        $sa = $sa->paginate(20);
        $symb = $symb->get();

        foreach($symb as $sy){
            $symbol = Symbol::where('id',$sy->symbol_id)->orderBy('updated_at','DESC')->first();
            if($symbol!=null){
                $sy->name = $symbol->name;
            }
        }
        $feedNavbarItems = Helpers::feedNavbarItems();
        $feedNavbarItems['trade']['is_active'] = true;

        return view('home.partials.trade-preview', ['trades' => $symb])
        .view('home.partials.pagination', [
            'paginator' => $sa,
            'page_number' => request()->page ?? 1
        ])
            .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
            .view('components.htmx.head', [
                'page_title' => ''
            ]);
    }

    public function search(Request $request)
    {
        if($request->content==null){
            $symbols = Symbol::orderBy('updated_at', 'desc')->paginate(20);
            return view('home.partials.symbol-preview', ['symbol' => $symbols])
            .view('home.partials.pagination', [
                'paginator' => $symbols,
                'page_number' => request()->page ?? 1
            ])
            .view('home.partials.search-item-list', ['symbols' => $symbols]); 
            return view('home.partials.search-item-list', [
                'symbols' => $symbols
            ]);
        }
        
        $symbols = Symbol::Where('name','like',strtoupper($request->content)."%")->with(['favoritedUsers']);
        $symb = $symbols->paginate(5);
        $symbols = $symbols->get();
        $feedNavbarItems = Helpers::feedNavbarItems();
        $feedNavbarItems['global']['is_active'] = true;

        return view('home.partials.symbol-preview', ['symbol' => $symbols])
            .view('home.partials.pagination', [
                'paginator' => $symb,
                'page_number' => request()->page ?? 1
            ])
            .view('home.partials.search-item-list', ['symbols' => $symbols]);       
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
