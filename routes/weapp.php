<?php

Route::group(['prefix' => 'weapp', 'namespace' => 'WeApp'], function () {

    Route::group(['prefix' => 'user'], function () {
        Route::get('/login', 'UserController@login');
    });

    // 需要验证token
    Route::group(['middleware' => 'auth.weapp'], function () {
        Route::group(['prefix' => 'user'], function () {
            Route::get('/info', 'UserController@getUserInfo');
            Route::post('/info', 'UserController@updateUserInfo');

        });

        Route::group(['prefix' => 'question'], function () {
            Route::get('/subjects', 'QuestionController@getSubjects');
            Route::get('/chooseSubject', 'QuestionController@chooseSubject');
            Route::get('/modules', 'QuestionController@getModules');
            Route::get('/questions', 'QuestionController@getQuestions');
        });
    
    });

});

