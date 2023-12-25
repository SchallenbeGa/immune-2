<?php

namespace App\Http\Controllers\Htmx;

use App\Models\Symbol;
use App\Support\Helpers;
use Illuminate\Support\Str;
use App\Models\Trade;
use App\Http\Controllers\Controller;
use App\Models\Ohlvc;
use App\Models\Signal;

class HTMXSymbolController extends Controller
{
    public function show(Symbol $symbol)
    {   
        $trades = Trade::where('symbol_id',$symbol->id)->orderBy('updated_at','DESC')->get();
        $signals = Signal::where('symbol_id',$symbol->id)->orderBy('updated_at','DESC')->get();
 
        $oh=Ohlvc::where('symbol_id',$symbol->id)->get();
        $td =[];
        foreach($oh as $o){
            $td["open"][]=$o["open"];
            $td["high"][]=$o["high"];
            $td["low"][]=$o["low"];
            $td["close"][]=$o["close"];
            $td["x"][]=$o["slug"];
        }
        return view('symbol.partials.show', [
            'symbol' => $symbol,
            'oh'=> $td

        ])       
        .view('components.htmx.head', [
            'page_title' => Str::words($symbol->name, 40, '') . ' —']);
    
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
        $trades = Trade::where('symbol_id',$symbol->id)->orderBy('updated_at','DESC')->get();
        $signals = Signal::where('symbol_id',$symbol->id)->orderBy('updated_at','DESC')->get();
        $data=[$trades,$signals];
        return view('symbol.partials.signal-wrapper', [
            'data' => $data
        ]);
    }
}