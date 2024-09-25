<?php

namespace App\Support;

use App\Models\User;

class Helpers
{
    public static function feedNavbarItems(): array
    {
        $navbarItems = [];

        if (!auth()->guest()) {
           //
        }

        $navbarItems['global'] = [
            'title' => 'articles',
            'is_active' => false,
            'hx_get_url' => '/htmx/home/global-feed',
            'hx_push_url' => '/'
        ];

        return $navbarItems;
    }

   

    public static function redirectToHome()
    {
        return response()->view('components.redirect', [
                'hx_get' => '/htmx/home',
                'hx_target' => '#app-body',
                'hx_trigger' => 'load',
            ])
            ->withHeaders([
                'HX-Replace-Url' => '/',
                'HX-Reswap' => 'none'
            ]);
    }

    public static function redirectToSignIn()
    {
        return response()->view('components.redirect', [
                'hx_get' => '/htmx/sign-in',
                'hx_target' => '#app-body',
                'hx_trigger' => 'load',
            ])
            ->withHeaders([
                'HX-Push-Url' => '/sign-in',
                'HX-Reswap' => 'none'
            ]);
    }
}
