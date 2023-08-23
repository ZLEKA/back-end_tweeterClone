<?php
require_once( __DIR__. '/../../libs/Model/Model.php');
class Session extends Model{


    //data contains user_id & session_id
    static function newSession($data){
        if($session=Session::find('user_id',$data['user_id'])){   
            $session[0]->update($data);   
            Session::updateTime($session[0]->user_id);
        }else{
            Session::create($data);
        }
    }
    
    static function getSession(){
        //check sessio if is active 
        if(session_status()!=PHP_SESSION_ACTIVE)
            session_start();
        if(!isset($_SESSION['user_log_id']))
            return null;
        session_regenerate_id();    
        $data=[
            'user_id' => $_SESSION['user_log_id'],
            'session_data'=> session_id()
        ];
        Session::newSession($data);
        $session=Session::find('user_id',$_SESSION['user_log_id']);
        Session::updateTime($session[0]->id);
        return $session;
    }
    static function updateTime($id){
        $time=new DateTime("now");
        if($session=Session::find('id',$id)){
            $session[0]->updated_at=$time->format('Y-m-d H:i:s');
            $session[0]->save();
            return true;
        }
        return false;
    }
    public function user(){
        return $this->hasOne('User');
    }
}