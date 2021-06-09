<?php

require_once Env::app('MODELS').'Tweet.php';

class TweetController
{
    public function create(Request $request){
        $tweet = json_decode($request->content,true);
        $tweet = Tweet::create($tweet);
        Response::json($tweet,Response::HTTP_CREATED);
    }

    public function read(Request $request,$id){
        $tweet = Tweet::first('id',$id);
        Response::json($tweet,Response::HTTP_OK);
    }
    public function all(Request $request){
        $tweet = Tweet::all();
        Response::json($tweet,Response::HTTP_OK);
    }

    public function update(Request $request,$id){
        $tweet = Tweet::first('id',$id);
        $update = json_decode($request->content,true);
        $tweet->update($update);
        Response::json($tweet,Response::HTTP_OK);
    }

    public function delete(Request $request,$id){
        Tweet::destroy('id','=',$id);
        Response::code(Response::HTTP_OK);
    }


}