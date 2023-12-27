<?php

namespace App\Http\Controllers;

use App\Models\Symbol;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Ohlvc;
use App\Models\Trade;

class SymbolController extends Controller
{
    public function show(Symbol $symbol)
    {
        $oh=Ohlvc::where('symbol_id',$symbol->id)->get();
        $trades = Trade::where('symbol_id',$symbol->id)->get();

        $td =[];
        foreach($oh as $o){
            $td["open"][]=$o["open"];
            $td["high"][]=$o["high"];
            $td["low"][]=$o["low"];
            $td["close"][]=$o["close"];
            $td["x"][]=$o["slug"];
        }
        foreach($trades as $p){
            $e = $p->created_at->format('Y-m-d h:m:s');
            if($e>=$td["x"][0]){
                $td["date_".$p->side][] = $e;
                $td[$p->side][] = $p->price;
            }
        }
        
        return view('symbol.detail', [
            'symbol' => $symbol,
            'oh' => $td,
            'data' => $symbol->data()->get(),
            'page_title' => Str::words($symbol->name, 40, '') . ' —'
        ]);
    }
}
