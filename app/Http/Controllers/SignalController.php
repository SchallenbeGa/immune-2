<?php

namespace App\Http\Controllers;

use App\Models\Signal;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class SignalController extends Controller
{
    public function show(Signal $signal)
    {

        return view('signal.detail', [
            'symbol' => $signal,
            'data' => $signal->data()->get(),
            
            'page_title' => Str::words($signal->name, 40, '') . ' —'
        ]);
    }
}
