<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\DashboardController;

Route::get('/', [BlogController::class, 'index'])->name('blog.index');

// Blog Routes
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/pesquisar', [BlogController::class, 'search'])->name('search');
    Route::get('/categoria/{category:slug}', [BlogController::class, 'category'])->name('category.show');
    Route::get('/tag/{tag}', [BlogController::class, 'tag'])->name('tag.show');
    Route::get('/{post:slug}', [BlogController::class, 'show'])->name('post.show');
    
    // Newsletter
    Route::post('/newsletter/subscribe', [BlogController::class, 'newsletterSubscribe'])->name('newsletter.subscribe');
    Route::get('/newsletter/unsubscribe/{token}', [BlogController::class, 'newsletterUnsubscribe'])->name('newsletter.unsubscribe');
    
    // Comments
    Route::post('/post/{post}/comment', [BlogController::class, 'storeComment'])->name('comment.store');
    
    // Petition Signatures
    Route::post('/post/{post}/petition-signature', [BlogController::class, 'storePetitionSignature'])->name('petition.signature.store');
});

// Poll Routes
Route::prefix('polls')->name('polls.')->group(function () {
    Route::post('/{poll}/vote', [PollController::class, 'vote'])->name('vote');
    Route::post('/{poll}/revote', [PollController::class, 'revote'])->name('revote');
    Route::get('/{poll}/results', [PollController::class, 'results'])->name('results');
});

// Download Routes
Route::prefix('downloads')->name('downloads.')->group(function () {
    Route::get('/', [DownloadController::class, 'index'])->name('index');
    Route::get('/categoria/{category}', [DownloadController::class, 'category'])->name('category');
    Route::get('/{download}/download', [DownloadController::class, 'download'])->name('download');
    Route::get('/{download}', [DownloadController::class, 'show'])->name('show');
});

// Client Routes
Route::prefix('cliente')->name('client.')->group(function () {
    // Guest routes
    Route::middleware('guest:client')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/cadastro', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/cadastro', [AuthController::class, 'register']);
    });
    
    // AJAX routes for modals
    Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
    
    // Authenticated routes
    Route::middleware('auth:client')->group(function () {
        Route::get('/painel', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
        // Profile routes
        Route::get('/perfil', [DashboardController::class, 'profile'])->name('profile');
        Route::post('/perfil', [DashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/perfil/senha', [DashboardController::class, 'updatePassword'])->name('password.update');
        
        // Address routes
        Route::get('/enderecos', [DashboardController::class, 'addresses'])->name('addresses');
        Route::post('/enderecos', [DashboardController::class, 'storeAddress'])->name('addresses.store');
        Route::put('/enderecos/{address}', [DashboardController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/enderecos/{address}', [DashboardController::class, 'deleteAddress'])->name('addresses.delete');
        
        // Preferences routes
        Route::get('/preferencias', [DashboardController::class, 'preferences'])->name('preferences');
        Route::post('/preferencias', [DashboardController::class, 'updatePreferences'])->name('preferences.update');
    });
});

// Admin redirect
