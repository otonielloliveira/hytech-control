<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CourseController;

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

// Lecture Routes (Palestras)
Route::prefix('palestras')->name('lectures.')->group(function () {
    Route::get('/', [LectureController::class, 'index'])->name('index');
    Route::get('/{lecture}', [LectureController::class, 'show'])->name('show');
});

// Album Routes (Álbuns de Fotos)
Route::prefix('albuns')->name('albums.')->group(function () {
    Route::get('/', [AlbumController::class, 'index'])->name('index');
    Route::get('/{album}', [AlbumController::class, 'show'])->name('show');
});

// Video Routes (Vídeos)
Route::prefix('videos')->name('videos.')->group(function () {
    Route::get('/', [VideoController::class, 'index'])->name('index');
    Route::get('/{video}', [VideoController::class, 'show'])->name('show');
});

// Course Routes (Cursos)
Route::prefix('cursos')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/{slug}', [CourseController::class, 'show'])->name('show');
    
    // Enrollment and learning routes (authenticated clients only)
    Route::middleware('auth:client')->group(function () {
        Route::post('/{slug}/matricular', [CourseController::class, 'enroll'])->name('enroll');
        Route::get('/{slug}/aprender', [CourseController::class, 'learning'])->name('learning');
        Route::get('/{courseSlug}/modulo/{moduleSlug}/aula/{lessonSlug}', [CourseController::class, 'lesson'])->name('lesson');
        Route::post('/{courseSlug}/modulo/{moduleSlug}/aula/{lessonSlug}/completar', [CourseController::class, 'markLessonCompleted'])->name('lesson.complete');
        Route::get('/{slug}/certificado', [CourseController::class, 'downloadCertificate'])->name('certificate');
        Route::get('/meus-cursos', [CourseController::class, 'myCourses'])->name('my-courses');
    });
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
        
        // Orders routes
        Route::get('/pedidos', [DashboardController::class, 'orders'])->name('orders');
        Route::get('/pedidos/{order}', [DashboardController::class, 'orderDetail'])->name('orders.detail');
        
        // Preferences routes
        Route::get('/preferencias', [DashboardController::class, 'preferences'])->name('preferences');
        Route::post('/preferencias', [DashboardController::class, 'updatePreferences'])->name('preferences.update');
    });
});

// Store Routes (Loja)
Route::prefix('loja')->name('store.')->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('index');
    Route::get('/produto/{slug}', [StoreController::class, 'show'])->name('product');
    
    // Cart routes
    Route::get('/carrinho', [StoreController::class, 'cart'])->name('cart');
    Route::post('/carrinho/adicionar/{product}', [StoreController::class, 'addToCart'])->name('cart.add');
    Route::patch('/carrinho/atualizar', [StoreController::class, 'updateCart'])->name('cart.update');
    Route::delete('/carrinho/remover', [StoreController::class, 'removeFromCart'])->name('cart.remove');
    Route::delete('/carrinho/limpar', [StoreController::class, 'clearCart'])->name('cart.clear');
    
    // Checkout routes
    Route::get('/checkout', [StoreController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/processar', [StoreController::class, 'processCheckout'])->name('checkout.process');
    
    // Order routes
    Route::get('/pedido/{order}/sucesso', [StoreController::class, 'orderSuccess'])->name('order.success');
    
    // AJAX routes for shipping calculation
    Route::post('/frete/calcular', [StoreController::class, 'calculateShipping'])->name('shipping.calculate');
    Route::post('/frete/aplicar', [StoreController::class, 'applyShipping'])->name('shipping.apply');
});

// Admin redirect

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

// Lecture Routes (Palestras)
Route::prefix('palestras')->name('lectures.')->group(function () {
    Route::get('/', [LectureController::class, 'index'])->name('index');
    Route::get('/{lecture}', [LectureController::class, 'show'])->name('show');
});

// Album Routes (Álbuns de Fotos)
Route::prefix('albuns')->name('albums.')->group(function () {
    Route::get('/', [AlbumController::class, 'index'])->name('index');
    Route::get('/{album}', [AlbumController::class, 'show'])->name('show');
});

// Video Routes (Vídeos)
Route::prefix('videos')->name('videos.')->group(function () {
    Route::get('/', [VideoController::class, 'index'])->name('index');
    Route::get('/{video}', [VideoController::class, 'show'])->name('show');
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
        
        // Orders routes
        Route::get('/pedidos', [DashboardController::class, 'orders'])->name('orders');
        Route::get('/pedidos/{order}', [DashboardController::class, 'orderDetail'])->name('orders.detail');
        
        // Preferences routes
        Route::get('/preferencias', [DashboardController::class, 'preferences'])->name('preferences');
        Route::post('/preferencias', [DashboardController::class, 'updatePreferences'])->name('preferences.update');
    });
});

// Store Routes (Loja)
Route::prefix('loja')->name('store.')->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('index');
    Route::get('/produto/{slug}', [StoreController::class, 'show'])->name('product');
    
    // Cart routes
    Route::get('/carrinho', [StoreController::class, 'cart'])->name('cart');
    Route::post('/carrinho/adicionar/{product}', [StoreController::class, 'addToCart'])->name('cart.add');
    Route::patch('/carrinho/atualizar', [StoreController::class, 'updateCart'])->name('cart.update');
    Route::delete('/carrinho/remover', [StoreController::class, 'removeFromCart'])->name('cart.remove');
    Route::delete('/carrinho/limpar', [StoreController::class, 'clearCart'])->name('cart.clear');
    
    // Checkout routes
    Route::get('/checkout', [StoreController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/processar', [StoreController::class, 'processCheckout'])->name('checkout.process');
    
    // Order routes
    Route::get('/pedido/{order}/sucesso', [StoreController::class, 'orderSuccess'])->name('order.success');
    
    // AJAX routes for shipping calculation
    Route::post('/frete/calcular', [StoreController::class, 'calculateShipping'])->name('shipping.calculate');
    Route::post('/frete/aplicar', [StoreController::class, 'applyShipping'])->name('shipping.apply');
});

// Admin redirect
