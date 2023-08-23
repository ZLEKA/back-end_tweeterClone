<?php
require_once( __DIR__. '/../../libs/Model/Model.php');
class User extends Model{
   public function tweets(){
    return $this->hasMany('Tweet');
   }
}