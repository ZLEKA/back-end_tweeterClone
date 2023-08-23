<?php

Router::get('/', ['use' => 'HomeController@home']); 
Router::get('/auth/registerView', ['use' => 'AuthController@registerView']);
Router::get('/auth/loginView', ['use' => 'AuthController@loginView']);
Router::get('/tweet/dashboard', ['use' => 'TweetView@dashboardView']);
//-----------------------APIs--------------------------//
Router::post('/api/auth/register', ['use' => 'AuthController@register']);
Router::post('/api/auth/login', ['use' => 'AuthController@login']);
Router::post('/api/auth/logout', ['use' => 'AuthController@logout']);
Router::post('/api/tweet', ['use' => 'TweetController@createTweet']);
Router::post('/api/tweet/comment', ['use' => 'TweetController@commentTweet']);
Router::delete('/api/tweet/delete/{id}', ['use' => "TweetController@deleteTweet"]);
Router::post('/api/tweet/update', ['use' => 'TweetController@updateTweet']);
Router::get('/api/tweet/getUserByTweet/{id}', ['use' => 'PublicController@getUserByTweet']);
Router::get('/api/tweet/getTweetComments/{id}', ['use' => 'PublicController@getTweetComments']);
Router::get('/api/tweet/getUserTweet', ['use' => 'PublicController@getAllMyTweets']);
Router::get('/api/tweet/getAllTweets',['use'=>'PublicController@getAllTweets']);
Router::get('/api/tweet/user', ['use'=>'TweetController@user']);
Router::get('/api/tweet/getTweet/{id}', ['use'=>'PulicController@getTweet']);
Router::get('/api/tweet/like/{id}', ['use'=>'TweetController@like']);
