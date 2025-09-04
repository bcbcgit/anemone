<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\CharacterPointController;
use App\Http\Controllers\ElementController;
use App\Http\Controllers\KindController;
use App\Http\Controllers\ScenarioController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// ------- guest 専用 -------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    // ユーザー登録
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');

    // ルートは login へ
    Route::get('/', fn () => redirect()->route('login'));
});

// ------- auth 専用 -------
Route::middleware('auth')->group(function () {
    // シナリオ
    Route::resource('scenarios', ScenarioController::class);

    // シナリオ分類
    Route::resource('kinds', KindController::class)->only(['index','store','update','destroy']);
    Route::post('/kinds/inline', [KindController::class, 'inlineStore'])->name('kinds.inline');

    // シナリオ要素
    Route::resource('elements', ElementController::class)->only(['index','store','update','destroy']);
    Route::post('/elements/inline', [ElementController::class, 'inlineStore'])->name('elements.inline');

    // キャラクター一覧
    Route::get('/characters', [CharacterPointController::class, 'index'])->name('characters.index');

    // キャラクター登録
    Route::resource('characters', CharacterController::class)->only(['create','store']);

    // ポイント+1（必要なら+Nに拡張）
    Route::post('/characters/{character}/increment', [CharacterPointController::class, 'increment'])
        ->name('characters.increment');

    // 未使用チケットを1枚消費
    Route::post('/characters/{character}/tickets/use-one', [TicketController::class, 'useOne'])
        ->name('tickets.useOne');

    // ログアウト
    Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
});
