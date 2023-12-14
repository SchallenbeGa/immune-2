<?php

namespace App\Http\Controllers;

use App\Models\Symbol;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Ohlvc;

class SymbolController extends Controller
{
    public function show(Symbol $symbol)
    {
        $oh=Ohlvc::where('symbol_id',$symbol->id)->get();
        return view('symbol.detail', [
            'symbol' => $symbol,
            'oh' => $oh,
            'data' => $symbol->data()->get(),
            'page_title' => Str::words($symbol->name, 40, '') . ' —'
        ]);
    }
}
