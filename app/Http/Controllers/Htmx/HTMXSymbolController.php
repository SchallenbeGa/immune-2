<?php

namespace App\Http\Controllers\Htmx;

use App\Models\Symbol;
use App\Support\Helpers;
use Illuminate\Support\Str;
use App\Models\Trade;
use App\Http\Controllers\Controller;
use App\Models\Ohlvc;
use App\Models\Signal;
use Illuminate\Support\Carbon;

class HTMXSymbolController extends Controller
{
    public function show(Symbol $symbol)
    {   
        $trades = Trade::where('symbol_id',$symbol->id)->get();
        $signals = Signal::where('symbol_id',$symbol->id)->orderBy('updated_at','DESC')->limit(50)->get();
 
        $oh=Ohlvc::where('symbol_id',$symbol->id)->get();
        $td =[];
        foreach($oh as $o){
            $td["open"][]=$o["open"];
            $td["high"][]=$o["high"];
            $td["low"][]=$o["low"];
            $td["close"][]=$o["close"];
            $td["x"][]=Carbon::createFromFormat('Y-m-d H:i:s', $o->slug)->format('Y-m-d H:i');
        }
        foreach($trades as $p){
            $e = $p->created_at->format('Y-m-d H:i');
            if($e>=$td["x"][0]){
                $td["date_".$p->side][] = $e;
                $td[$p->side][] = $p->price;
            }
        }
        return view('symbol.partials.show', [
            'symbol' => $symbol,
            'oh'=> $td
        ]);
    
    }
    public function favorite(Symbol $symbol)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }

        $isSymbolFavoritedByUser = $symbol->toggleUserFavorite(auth()->user());
        dd($isSymbolFavoritedByUser);
        return view('symbol.partials.favorite-button', [
            'symbol' => $symbol,
            'favorite_count' => $symbol->favoritedUsers->count(),
            'is_favorited' => $isSymbolFavoritedByUser,
            'oob_swap' => true
        ]);
    }
    public function data(Symbol $symbol)
    {
        $trades = Trade::where('symbol_id',$symbol->id)->get();
        $signals = Signal::where('symbol_id',$symbol->id)->orderBy('updated_at','DESC')->limit(50)->get();
        $oh=Ohlvc::where('symbol_id',$symbol->id)->limit(1)->get();
        if(count($trades)>0){
            $prev =null;
            foreach($trades as $p){
                if ($p->side == "SELL"){      
                    $p->profit = ($p->price - $prev)*(1000/$oh[0]->close);
                }else{
                    $p->profit = "";
                }
                $prev = $p->price;
            }
        }

        $data=[$trades->reverse(),$signals];
        return view('symbol.partials.signal-wrapper', [
            'data' => $data
        ]);
    }
}