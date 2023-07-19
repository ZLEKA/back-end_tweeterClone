<?php
require_once( __DIR__. '/../../Models/User.php');
require_once( __DIR__. '/../../Models/Session.php');
require_once( __DIR__. '/../../Models/Tweet.php');
require_once( __DIR__. '/../../Models/TweetComment.php');
require_once('AuthController.php');
class TweetController extends Controller{
    public function createTweet(Request $request){
        try{
            $session=Session::getSession();
            
            if(empty($session))
                return Response::json(AuthController::NotLogged,Response::HTTP_UNAUTHORIZED);
            
            $param=array("user_id","content");
            
            $sanitaizer=AuthController::contolData($request,$param);
            var_dump($sanitaizer);
            if(empty($sanitaizer))
                return Response::json(AuthController::NotWork,Response::HTTP_BAD_REQUEST); 
            
            $data=[
                'content'=> $sanitaizer['content'],
                'user_id'=> $session[0]->user_id,
            ];
            
            Tweet::create($data);
        }catch(Exception $e){
            return $this->jsonResponse([$e], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function commentTweet(Request $request){
        try{
            $session=Session::getSession();
            if(empty($session))
                return Response::json(AuthController::NotWork,Response::HTTP_UNAUTHORIZED);
        
            $param=array("content","tweet_id");
            $sanitaizer=AuthController::contolData($request,$param);
            $tweet=Tweet::getTweet($sanitaizer['tweet_id']);
            if(empty($tweet))
                return Response::json(AuthController::TweetNotExist,Response::HTTP_BAD_REQUEST); 
            
            $data=[
                'tweet_id'=> $sanitaizer['tweet_id'],
                'content'=> $sanitaizer['content']
            ];
            TweetComment::create($data);
            
        }catch(Exception $e){
            return $this->jsonResponse([$e], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteTweet(Request $request,$id){
        try{
            $session=Session::getSession();
            
            if($session instanceof Response)
                return $session;
            
            $id=stripcslashes(trim($id));
            if(!isset($id) || $id=='')
                return Response::json(AuthController::NotWork,Response::HTTP_BAD_REQUEST);
            
            $tweet=Tweet::getTweet($id);
            if(empty($tweet))
                return Response::json(AuthController::TweetNotExist,Response::HTTP_BAD_REQUEST);
            
            if($tweet[0]->user_id!=$session[0]->user_id)
                return Response::json(AuthController::UnauthorizedUpdate,Response::HTTP_UNAUTHORIZED);
                
            Tweet::destroy('id','=',$tweet[0]->id);
        }catch(Exception $e){
            return $this->jsonResponse([$e], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateTweet(Request $request){
        try{
            $session=Session::getSession();
            if(!isset($session))
                return Response::json(AuthController::NotLogged,Response::HTTP_UNAUTHORIZED);
                
            
            $param=array("tweet_id","content");
            $sanitaizer=AuthController::contolData($request,$param);
            if(empty($sanitaizer))
                return  Response::json(AuthController::NotWork,Response::HTTP_BAD_REQUEST);
                
            $tweet=Tweet::getTweet($sanitaizer['tweet_id']);
            if(empty($tweet))
                return  Response::json(AuthController::NotWork,Response::HTTP_BAD_REQUEST);
            
            
            if($session[0]->user_id!=$tweet[0]->user_id)
                return Response::json(['message'=>'you can\'t modify this tweet '],Response::HTTP_BAD_REQUEST);
                
            Tweet::updateTweet($sanitaizer['tweet_id'],$sanitaizer['content']);
            
        }catch(Exception $e){
            return $this->jsonResponse([$e], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}