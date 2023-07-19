<?php
require_once( __DIR__. '/../../libs/Model/Model.php');
class User extends Model{
    static function getUsername($id){
        $username=User::find('id',$id);
        if(!isset($username))
            return Response::json(['message'=>"no user found"],Response::HTTP_BAD_REQUEST);
        
        return $username;
    }
}