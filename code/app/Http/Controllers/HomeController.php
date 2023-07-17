<?php

class HomeController extends Controller
{
    public function home(Request $request){
        $app = Env::get('APP_NAME');
        return new View('home.php',compact('app'));
    }
}