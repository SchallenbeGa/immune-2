<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Helpers;

class UserController extends Controller
{
    public function show(User $user)
    {
        $user->load(['favorites']);
        return view('users.show', [
            'user' => $user,
            'navbar_active' => 'profile',
            'symbols' => $user->favorites,
            'personal' => true,
            'page_title' => 'Your profil —'
        ]);
    }

    public function favorites(User $user)
    {

        return view('users.show', [
            'user' => $user,
            'personal' => true,
            'page_title' => 'Your feed —'
        ]);
    }

}
