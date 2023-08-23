<?php
require_once( __DIR__. '/../../Models/User.php');
require_once( __DIR__. '/../../Models/Session.php');
require_once( __DIR__. '/../../Models/Tweet.php');
require_once( __DIR__. '/../../Models/TweetComment.php');
require_once('AuthController.php');;
class TweetView extends Controller{
    private $session; 
    public function __construct(){
        $this->session=Session::getSession();
    }
    public function dashboardView(){
       
        return new View('dashboard.php');
    }
    
}