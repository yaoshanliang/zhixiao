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

        Route::group(['prefix' => 'subject'], function () {
            Route::get('/getAllSubjects', 'SubjectController@getAllSubjects');
            Route::get('/chooseSubject', 'SubjectController@chooseSubject');
            Route::get('/getMySubject', 'SubjectController@getMySubject');
        });

        Route::group(['prefix' => 'question'], function () {
            Route::get('/getQuestions', 'QuestionController@getQuestions');
        });
    
    });

});

