<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;

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

// Admin redirect
