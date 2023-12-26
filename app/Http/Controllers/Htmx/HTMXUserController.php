<?php

namespace App\Http\Controllers\Htmx;

use App\Models\User;
use App\Models\Article;
use App\Models\Ohlvc;
use App\Models\Signal;
use App\Models\Symbol;
use App\Models\Trade;
use App\Support\Helpers;
use App\Http\Controllers\Controller;

class HTMXUserController extends Controller
{
    public function show(User $user)
    {
        $user->load(['favorites']);
        $data = [];
        $fees = 0;
        $total_profit = 0;
        foreach($user->favorites as $sy){
            $pnl = "0.00 %";
            $sy->pnl = $pnl;
            $sy->profit = "0";
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
        return view('users.partials.show', [
            'user' => $user,
            'symbols' => $user->favorites,
            'personal' => true,
            'total'=>0,'invested_on'=>0,'total_invested'=>($user->favorites->count()*1000),
            'page_title' => 'Profil -'
        ])
        .view('users.partials.symbol-preview', [
            'symbols' => $user->favorites,
            'total'=>$total_profit,'invested_on'=>$data[0]->created_at ?? 0,'total_invested'=>($user->favorites->count()*1000),
            'is_current_user' => $user->isSelf
        ]);
    }

    public function favoriteSymbols(User $user)
    {
        $user->load(['favorites']);

        $userFeedNavbarItems = Helpers::userFeedNavbarItems($user);
        $userFeedNavbarItems['personal']['is_active'] = false;
        $userFeedNavbarItems['favorite']['is_active'] = true;

        $data = [];
        $fees = 0;
        $total_profit = 0;
        foreach($user->favorites as $sy){
            $pnl = "0.00 %";
            $sy->pnl = $pnl;
            $sy->profit = "0";
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

        return view('users.partials.symbol-preview', [
                'symbols' => $user->favorites,
                'total'=>$total_profit,'invested_on'=>$data[0]->created_at ?? 0,'total_invested'=>($user->favorites->count()*1000),
                'is_current_user' => $user->isSelf
        ]);
    }

    public function favorite(Article $article)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }

        // check if the current user are executing this function
        if (str_contains(request()->server()['HTTP_REFERER'], auth()->user()->username)) {
            $isDeleteItem = true;
        }

        $isArticleFavoritedByUser = $article->toggleUserFavorite(auth()->user());

        return response()->view('users.partials.favorite-button', [
            'article' => $article,
            'favorite_count' => $article->favoritedUsers->count(),
            'is_favorited' => $isArticleFavoritedByUser
        ]);
    }
    public function favorite_symbol(Symbol $symbol)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }

        // check if the current user are executing this function
        if (str_contains(request()->server()['HTTP_REFERER'], auth()->user()->username)) {
            $isDeleteItem = true;
        }

        $isArticleFavoritedByUser = $symbol->toggleUserFavorite(auth()->user());

        return response()->view('users.partials.favorite-button', [
            'symbol' => $symbol,
            'favorite_count' => $symbol->favoritedUsers->count(),
            'is_favorited' => $isSymbolFavoritedByUser
        ]);
    }
}
