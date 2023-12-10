<?php

namespace App\Http\Controllers;

use App\Models\Symbol;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class SymbolController extends Controller
{
    public function show(Symbol $symbol)
    {
        return view('symbol.detail', [
            'symbol' => $symbol,
            'data' => $symbol->data()->get(),
            'page_title' => Str::words($symbol->name, 40, '') . ' —'
        ]);
    }
}
