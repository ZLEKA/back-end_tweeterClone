<?php

Router::get('/',['use'=>'HomeController@home']);
Router::get('/auth',['use'=> 'AuthController@home']);
Router::post('/auth/register',['use'=> 'AuthController@register']);
Router::post('/auth/login',['use'=> 'AuthController@login']);
Router::post('/auth/logout', ['use'=> 'AuthController@logout']);
Router::post('/tweet',['use'=> 'TweetController@createTweet']);
Router::post('/tweet/comment',['use'=> 'TweetController@commentTweet' ]);
Router::delete('/tweet/delete/{id}', ['use'=>"TweetController@deleteTweet"]);
Router::post('/tweet/update', ['use'=> 'TweetController@updateTweet']);

