<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;

class SignInController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect('/');
        }

        return view('sign-in.index', [
            'navbar_active' => 'sign-in',
            'page_title' => 'Sign In —'
        ]);
    }
    public function signIn(SignInRequest $request)
    {
        $validated = $request->safe()->only(['email', 'password']);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || ! Hash::check($validated['password'], $user->password)) {

            $errors = new MessageBag([
                'email' => 'Email and password did not match'
            ]);

            return response()->view('components.form-error-message', [
                'errors' => $errors,
                'oldEmail' => $request->email
            ])
            ->withHeaders([
                'HX-Reswap' => 'innerHTML show:top',
                'HX-Retarget' => '#sign-in-form-messages'
            ]);
        }

        auth()->login($user);

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


    public function logout()
    {
        auth()->logout();

        return redirect('/');
    }
}