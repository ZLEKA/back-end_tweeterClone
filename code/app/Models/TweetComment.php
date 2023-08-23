<?php
require_once( __DIR__. '/../../libs/Model/Model.php');
class TweetComment extends Model{
    
    public function tweet() {
        return $this->hasOne('Tweet');
    }
    public function user(){
        return $this->hasOne('User');
    }
}