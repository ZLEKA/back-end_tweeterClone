<?php
require_once( __DIR__. '/../../libs/Model/Model.php');
class Tweet extends Model{
    public function user() {
        return $this->hasOne('User');
    } 
    public function comments(){
        return $this->hasMany('TweetComment');
    }
}