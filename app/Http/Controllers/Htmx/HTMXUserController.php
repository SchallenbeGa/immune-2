<?php

namespace App\Http\Controllers\Htmx;

use App\Models\User;
use App\Models\Article;
use App\Models\Symbol;
use App\Support\Helpers;
use App\Http\Controllers\Controller;

class HTMXUserController extends Controller
{
    public function show(User $user)
    {
        $user->load(['favorites']);

        return view('users.partials.show', [
            'user' => $user,
            'symbols' => $user->favorites,
            'personal' => true,
            'page_title' => 'Profil -'
        ])
        .view('components.navbar', ['navbar_active' => 'profile'])
        .view('users.partials.symbol-preview', [
            'symbols' => $user->favorites,
            'is_current_user' => $user->isSelf
        ]);
    }

    public function favoriteSymbols(User $user)
    {
        $user->load(['favorites']);

        $userFeedNavbarItems = Helpers::userFeedNavbarItems($user);
        $userFeedNavbarItems['personal']['is_active'] = false;
        $userFeedNavbarItems['favorite']['is_active'] = true;

        return view('users.partials.symbol-preview', [
                'symbols' => $user->favorites,
                'is_current_user' => $user->isSelf
        ]);
    }

    public function favorite(Article $article)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }

        // check if the current user are executing this function
        if (str_contains(request()->server()['HTTP_REFERER'], auth()->user()->username)) {
            $isDeleteItem = true;
        }

        $isArticleFavoritedByUser = $article->toggleUserFavorite(auth()->user());

        return response()->view('users.partials.favorite-button', [
            'article' => $article,
            'favorite_count' => $article->favoritedUsers->count(),
            'is_favorited' => $isArticleFavoritedByUser
        ]);
    }
    public function favorite_symbol(Symbol $symbol)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }

        // check if the current user are executing this function
        if (str_contains(request()->server()['HTTP_REFERER'], auth()->user()->username)) {
            $isDeleteItem = true;
        }

        $isArticleFavoritedByUser = $symbol->toggleUserFavorite(auth()->user());

        return response()->view('users.partials.favorite-button', [
            'symbol' => $symbol,
            'favorite_count' => $symbol->favoritedUsers->count(),
            'is_favorited' => $isSymbolFavoritedByUser
        ]);
    }
}
