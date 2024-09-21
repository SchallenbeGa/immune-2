<?php

namespace App\Http\Controllers\Htmx;

use App\Support\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingUpdateRequest;

class HTMXSettingsController extends Controller
{
    public function index()
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }

        $user = auth()->user();

        return view('settings.partials.index', ['user' => $user])
            .view('components.htmx.head', [
                'page_title' => 'Settings —'
            ]);
    }

    public function update(SettingUpdateRequest $request)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }

        $validated = $request->safe()->all();

        $data = [
            'image' => $validated['image_url'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'bio' => $validated['bio']
        ];

        if ($validated['password']) {
            $data['password'] = bcrypt($validated['password']);
        }

        $user = tap(auth()->user())->update($data);
   
        return response()->view('components.redirect', [
            'hx_get' => '/htmx/home',
            'hx_target' => '#app-body',
            'hx_trigger' => 'load',
        ])
        ->withHeaders([
            'HX-Redirect' => '/',
            'HX-Reswap' => 'none'
        ]);
    }
}
