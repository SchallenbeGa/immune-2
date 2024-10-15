<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Htmx\HTMXHomeController;
use App\Http\Controllers\Htmx\HTMXUserController;
use App\Http\Controllers\Htmx\HTMXEditorController;
use App\Http\Controllers\Htmx\HTMXSignInController;
use App\Http\Controllers\Htmx\HTMXSignUpController;
use App\Http\Controllers\Htmx\HTMXArticleController;
use App\Http\Controllers\Htmx\HTMXSettingsController;
use App\Http\Controllers\Htmx\HTMXImportController;
use App\Http\Controllers\Htmx\HTMXInventoryController;

use App\Http\Controllers\ImportController;
use App\Http\Controllers\InventoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index']);

Route::post('/import', [ImportController::class, 'import']);

Route::get('/sign-in', [SignInController::class, 'index'])->middleware('guest')->name('login');
Route::post('/sign-in', [SignInController::class, 'signIn'])->middleware('guest');
Route::get('/logout', [SignInController::class, 'logout'])->middleware('auth');

Route::get('/inventory', [InventoryController::class, 'index']);
Route::get('/sign-up', [SignUpController::class, 'index'])->middleware('guest');

Route::get('/settings', [SettingsController::class, 'index'])->middleware('auth');
Route::get('/computers/json', [InventoryController::class, 'getComputers'])->name('computers.json');
Route::get('/computers/{reference}', [InventoryController::class, 'showByReference'])->name('inventory.show');
Route::get('/computers/{reference}/json', [InventoryController::class, 'showByReferenceJson'])->name('inventory.showJson');
Route::prefix('htmx')->group(function() {

    Route::get('/home', [HTMXHomeController::class, 'index']);
    Route::get('/inventory/list', [HTMXInventoryController::class, 'showList']);
    Route::post('/home/articles/{article}/favorite', [HTMXHomeController::class, 'favorite']);
    Route::get('/computers/{reference}', [HTMXInventoryController::class, 'showByReference'])->name('inventory.show');
    Route::get('/articles/{article}', [HTMXArticleController::class, 'show']);
    Route::post('/articles/{article}/favorite', [HTMXArticleController::class, 'favorite']);
    Route::post('/articles/follow-user/{user}', [HTMXArticleController::class, 'follow']);
    Route::get('/articles/{article}/comments', [HTMXArticleController::class, 'comments']);
    Route::post('/articles/{article}/comments', [HTMXArticleController::class, 'postComment']);
    Route::delete('/articles/{article}', [HTMXArticleController::class, 'delete']);
    
    Route::get('/editor', [HTMXEditorController::class, 'create']);
    Route::post('/editor', [HTMXEditorController::class, 'store']);
    Route::get('/editor/{article}', [HTMXEditorController::class, 'edit']);
    Route::post('/editor/{article}', [HTMXEditorController::class, 'update']);
    
    Route::get('/popular-tags', [HTMXHomeController::class, 'popularTags']);

    Route::get('/sign-in', [HTMXSignInController::class, 'index']);
    Route::post('/sign-in', [HTMXSignInController::class, 'signIn']);
    Route::post('/logout', [HTMXSignInController::class, 'logout']);

    Route::get('/sign-up', [HTMXSignUpController::class, 'index']);
    Route::post('/sign-up', [HTMXSignUpController::class, 'signUp']);

    Route::get('/settings', [HTMXSettingsController::class, 'index']);
    Route::post('/settings', [HTMXSettingsController::class, 'update']);
    Route::post('/import', [HTMXImportController::class, 'import']);

    Route::get('/users/{user}', [HTMXUserController::class, 'show']);
    Route::get('/users/{user}/articles', [HTMXUserController::class, 'articles']);
    Route::get('/users/{user}/favorites', [HTMXUserController::class, 'favoriteArticles']);
    Route::post('/users/{user}/follow', [HTMXUserController::class, 'follow']);
    Route::post('/users/articles/{article}/favorite', [HTMXUserController::class, 'favorite']);
});