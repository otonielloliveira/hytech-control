<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\DownloadController;

Route::get('/', [BlogController::class, 'index'])->name('blog.index');

// Blog Routes
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/categoria/{category:slug}', [BlogController::class, 'category'])->name('category.show');
    Route::get('/tag/{tag}', [BlogController::class, 'tag'])->name('tag.show');
    Route::get('/{post:slug}', [BlogController::class, 'show'])->name('post.show');
    
    // Newsletter
    Route::post('/newsletter/subscribe', [BlogController::class, 'newsletterSubscribe'])->name('newsletter.subscribe');
    Route::get('/newsletter/unsubscribe/{token}', [BlogController::class, 'newsletterUnsubscribe'])->name('newsletter.unsubscribe');
    
    // Comments
    Route::post('/post/{post}/comment', [BlogController::class, 'storeComment'])->name('comment.store');
});

// Poll Routes
Route::prefix('polls')->name('polls.')->group(function () {
    Route::post('/{poll}/vote', [PollController::class, 'vote'])->name('vote');
    Route::get('/{poll}/results', [PollController::class, 'results'])->name('results');
});

// Download Routes
Route::prefix('downloads')->name('downloads.')->group(function () {
    Route::get('/', [DownloadController::class, 'index'])->name('index');
    Route::get('/categoria/{category}', [DownloadController::class, 'category'])->name('category');
    Route::get('/{download}/download', [DownloadController::class, 'download'])->name('download');
    Route::get('/{download}', [DownloadController::class, 'show'])->name('show');
});

// Admin redirect
