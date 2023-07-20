<?php
require_once( __DIR__. '/../../Models/User.php');
require_once( __DIR__. '/../../Models/Session.php');
require_once( __DIR__. '/../../Models/Tweet.php');
require_once( __DIR__. '/../../Models/TweetComment.php');
//require_once( __DIR__. '/../../Views/MyPages/register.php');
class AuthController extends Controller
{
    public const NotLogged=['message'=>"Not logged"];
    public const NotWork=['message'=>'Something go wrong'];
    public const UserAlredyExist=['message'=>"User already exist"];
    public const UnauthorizedUpdate=['message'=>'You can\'t delete this tweet'];
    public const TweetNotExist=['message'=>'Tweet not exist'];
    public const MailAlredyExist=['message'=>"Mail already exist"];
    public const TweetNotFound = ['message'=>"No tweet found"];
    public const PWNotEqual =['message' => "Password are different"];
    public function registerView(Request $request){
        return new View('register.php');
    }
    public function loginView(){
        return new View('login.php');
    }
    public function register(Request $request){
        try{
            $param=array("username","email","password","confirm_password");
            $sanitaizer=AuthController::contolData($request, $param);
            
            if(empty($sanitaizer))
                return Response::json(AuthController::NotWork,Response::HTTP_BAD_REQUEST); 
            
            if(User::where('email','=',$sanitaizer['email'])->get())
                return Response::json(AuthController::MailAlredyExist,Response::HTTP_BAD_REQUEST);
             
            if(User::where('username','=',$sanitaizer['username'])->get())
                return Response::json(AuthController::UserAlredyExist,Response::HTTP_BAD_REQUEST);
            
            if($sanitaizer['password']!= $sanitaizer['confirm_password'])
                return Response::json(AuthController::PWNotEqual,Response::HTTP_BAD_REQUEST);

            $data=[
                'username'=> $sanitaizer['username'],
                'email'=> $sanitaizer['email'],
                'password'=> md5($sanitaizer['password'])
            ];
            User::create($data); 
            
        }catch(Exception $e){
            return $this->jsonResponse([$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }      
    }

    public function login(Request $request){
        session_start();
        try{
            
            $param=["email", "password"];
            $sanitaizer=AuthController::contolData($request,$param);
            if(empty($sanitaizer))
                return Response::json(AuthController::NotWork,Response::HTTP_BAD_REQUEST); 
            
            $userData=User::where('email','=',$sanitaizer['email'])->get();
            if(empty($userData))
                return Response::json(AuthController::NotWork,Response::HTTP_BAD_REQUEST);
            
            if(md5($sanitaizer['password'])!=$userData[0]->password)
                return Response::json(AuthController::NotWork,Response::HTTP_BAD_REQUEST);
            
            $data=[
                'user_id'=>$userData[0]->id,
                'session_data'=>session_id(),
            ];
            $_SESSION['user_log_id']=$data['user_id'];
            Session::newSession($data);
        }catch(Exception $e){
            return $this->jsonResponse([$e], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function logout(){
        session_start();
        if(empty($_SESSION['user_log_id']))
            return Response::json(AuthController::NotLogged,Response::HTTP_UNAUTHORIZED);
                
        unset($_SESSION['user_log_id']);
        session_destroy();
    }
    
    //if $param is not empty mean some key are missing
    //$param have all required param
    static function contolData($rawData,$param){
        $req =$rawData->request;
        //cleaning data
        $sanitaizer=$req->all();
        //var_dump($sanitaizer);
        foreach($sanitaizer as $key=>$value) {
            $sanitaizer[$key]=stripcslashes(trim($value));
            $param=array_diff($param,array($key));
            if(!isset($sanitaizer[$key]) || empty($sanitaizer[$key] ))
                return null;
        }        
        return empty($param)?$sanitaizer:null;
    }
}    
