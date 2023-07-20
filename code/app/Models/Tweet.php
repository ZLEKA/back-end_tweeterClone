<?php
require_once( __DIR__. '/../../libs/Model/Model.php');
class Tweet extends Model{
    
    static function getTweet($id){
        $tweet=Tweet::find('id',$id);
        if(!isset($tweet))
            return null;
        
        return $tweet;
    }
    static function updateTweet($id,$content){
        //$tweet=Tweet::getTweet($id);
        $tweet=Tweet::find('id',$id);
        var_dump($tweet);
        if($tweet==null)
            return null;
        
        $time=new DateTime("now");
        $tweet[0]->content=$content;
        $tweet[0]->updated_at=$time->format('Y-m-d H:i:s');
        $tweet[0]->save();    
        return true;

    }
}