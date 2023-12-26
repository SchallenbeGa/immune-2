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
            'total'=>0,'invested_on'=>0,'total_invested'=>($user->favorites->count()*1000),
            'personal' => true,
            'page_title' => 'Your profil —'
        ]);
    }

    public function favorites(User $user)
    {

        return view('users.show', [
            'user' => $user,
            'total'=>0,'invested_on'=>0,'total_invested'=>($user->favorites->count()*1000),
            'personal' => true,
            'page_title' => 'Your feed —'
        ]);
    }

}
