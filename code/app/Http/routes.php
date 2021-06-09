<?php


Router::post('/tweet',['use'=>'TweetController@create']);
Router::get('/tweet/{id}',['use'=>'TweetController@read']);
Router::get('/tweet',['use'=>'TweetController@all']);
Router::patch('/tweet/{id}',['use'=>'TweetController@update']);
Router::delete('/tweet/{id}',['use'=>'TweetController@delete']);

Router::get('/123',['use'=>'HomeController@home']);