<?php

namespace App\Http\Controllers\Htmx;

use App\Models\Signal;
use App\Support\Helpers;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class HTMXSignalController extends Controller
{
    public function show(Signal $signal)
    {
        return view('symbol.partials.show', [
            'symbol' => $signal,
        ])
        .view('components.navbar', ['navbar_active' => ''])
        .view('components.htmx.head', [
            'page_title' => Str::words($signal->name, 40, '') . ' —'
        ]);
    }
}