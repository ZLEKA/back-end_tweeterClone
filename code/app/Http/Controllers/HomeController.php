<?php

require_once Env::app('MODELS').'Tweet.php';

class HomeController
{
    public function home(Request $request){
        $tweets = Tweet::all();
        return new View('home.php',compact('tweets'));
    }
}