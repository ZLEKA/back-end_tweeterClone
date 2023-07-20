<?php

Router::get('/',['use'=>'HomeController@home']);
Router::get('/auth/registerView',['use'=> 'AuthController@registerView']);
Router::get('/auth/loginView', ['use'=>'AuthController@loginView'] );
Router::get('/tweet/dashboard',['use'=> 'TweetController@dashboardView']);
//-----------------------APIs--------------------------//
Router::post('/api/auth/register',['use'=> 'AuthController@register']);
Router::post('/api/auth/login',['use'=> 'AuthController@login']);
Router::post('/api/auth/logout', ['use'=> 'AuthController@logout']);
Router::post('/api/tweet',['use'=> 'TweetController@createTweet']);
Router::post('/api/tweet/comment',['use'=> 'TweetController@commentTweet' ]);
Router::delete('/api/tweet/delete/{id}', ['use'=>"TweetController@deleteTweet"]);
Router::post('/api/tweet/update/{id}', ['use'=> 'TweetController@updateTweet']);


