<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/blogs');//Redireciona para o painel admin do Filament
});
