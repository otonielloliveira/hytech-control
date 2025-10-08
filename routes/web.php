<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');//Redireciona para o painel admin do Filament
});
