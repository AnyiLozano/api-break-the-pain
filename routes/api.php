<?php

use Illuminate\Support\Facades\Route;

Route::prefix("auth")->group(function(){
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
    Route::post('/edit-user', 'AuthController@editUser');
});

Route::prefix("levels")->group(function(){
    Route::post("/set-levels", "LevelController@setLevel")->middleware("auth:api");
    Route::get("/get-levels", "LevelController@getLevels")->middleware("auth:api");
    Route::post('/set-satisfaction', "LevelController@setSatisfaction")->middleware("auth:api");
});
