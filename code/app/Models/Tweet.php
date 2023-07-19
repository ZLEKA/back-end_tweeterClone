<?php
require_once( __DIR__. '/../../libs/Model/Model.php');
class Tweet extends Model{
    
    static function getTweet($id){
        
        $tweet=Tweet::find('id',$id);
        if(!isset($tweet))
            return Response::json(AuthController::TweetNotFound,Response::HTTP_BAD_REQUEST);
        
        return $tweet;
    }
    static function updateTweet($id,$content){
        //$tweet=Tweet::getTweet($id);
        $tweet=Tweet::find('id',$id);
        if($tweet==null)
            return Response::json(AuthController::TweetNotFound,Response::HTTP_BAD_REQUEST);
        
        $time=new DateTime("now");
        $tweet[0]->content=$content;
        $tweet[0]->updated_at=$time->format('Y-m-d H:i:s');
        $tweet[0]->save();    
        return true;

    }
}