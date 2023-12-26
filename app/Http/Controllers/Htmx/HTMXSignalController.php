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
        ]);
    }
}