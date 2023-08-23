<?php
require_once( __DIR__. '/../../Models/User.php');
require_once( __DIR__. '/../../Models/Session.php');
require_once( __DIR__. '/../../Models/Tweet.php');
require_once( __DIR__. '/../../Models/TweetComment.php');
require_once('AuthController.php');
require_once('PublicController.php');
class TweetController extends Controller{
    //param => user_id & session_data
    private $session;
    public function __construct(){
        $this->session=Session::getSession();
        if(empty($this->session)){
            Response::json(AuthController::NotLogged,Response::HTTP_UNAUTHORIZED);    
            exit();
        }
    }
    //++++++++++++++++++++++++CRUD++++++++++++++++++++++++++++++++++++
    public function createTweet(Request $request){
        try{
            $param=array("content");
            $sanitaizer=AuthController::contolData($request,$param);
            
            if(!isset($sanitaizer))
                return Response::json(AuthController::NotWork,Response::HTTP_BAD_REQUEST); 
            
            $data=[
                'content'=> $sanitaizer['content'],
                'user_id'=> $this->session[0]->user_id,
                //'user' => $this->session[0]->user()
            ];
            //var_dump($data)
            $data1 = [
                'tweet' => Tweet::create($data),
                'user' => $this->session[0]->user()
            ];
            return Response::json($data1);
 
        }catch(Exception $e){
            return $this->jsonResponse([$e], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function commentTweet(Request $request){
        try{
            $param=array("content","tweet_id");
            $sanitaizer=AuthController::contolData($request,$param);
            $tweet=PublicController::getTweet($sanitaizer['tweet_id']);
            if(empty($tweet))
                return Response::json(AuthController::TweetNotExist,Response::HTTP_BAD_REQUEST); 
            
            $data=[
                'tweet_id'=> $sanitaizer['tweet_id'],
                'content'=> $sanitaizer['content'],
                'user_id'=>$this->session[0]->user_id
            ];
            return Response::json(TweetComment::create($data));
            
        }catch(Exception $e){
            return $this->jsonResponse([$e], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteTweet(Request $request,$id){
        try{
            $id=stripcslashes(trim($id));
            if(!isset($id) || $id=='')
                return Response::json(AuthController::NotWork,Response::HTTP_BAD_REQUEST);
            
            $tweet=PublicController::getTweet($id);
            if(empty($tweet))
                return Response::json(AuthController::TweetNotExist,Response::HTTP_BAD_REQUEST);
            
            if($tweet[0]->user_id!=$this->session[0]->user_id)
                return Response::json(AuthController::UnauthorizedUpdate,Response::HTTP_UNAUTHORIZED);
                
            return Response::json(Tweet::destroy('id','=',$tweet[0]->id));
        }catch(Exception $e){
            return $this->jsonResponse([$e], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateTweet(Request $request){
        
        try{
            
            $param=array("tweet_id","content");
            $sanitaizer=AuthController::contolData($request,$param);
            if(empty($sanitaizer))
                return  Response::json(AuthController::NotWork,Response::HTTP_BAD_REQUEST);
                
            $tweet=PublicController::getTweet($sanitaizer['tweet_id']);
            if(empty($tweet))
                return  Response::json(AuthController::NotWork,Response::HTTP_BAD_REQUEST);
            
            if($this->session[0]->user_id!=$tweet[0]->user_id)
                return Response::json(['message'=>'you can\'t modify this tweet '],Response::HTTP_BAD_REQUEST);
                
            return Response::json(TweetController::update($sanitaizer['tweet_id'],$sanitaizer['content']));
        }catch(Exception $e){
            return $this->jsonResponse([$e], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function like(Request $request, $id){
        
        if(LikeTable::whereRaw('tweet_id = '. $id .' AND user_id = '.$this->session[0]->user_id)->get()){
            return Response::json(LikeTable::destroy('tweet_id', "=", $id));
        }else {
            $data= [
                'user_id' => $this->session[0]->user_id,
                'tweet_id' => $id
            ];
            return Response::json(LikeTable::create($data));
        }
    }
    //++++++++++++++++++++++++++++++++++++++++CRUD++++++++++++++++++++++++++++++++
    //*********************************STATIC FUNCTION************************ */
    
    static function update($id,$content){
        //$tweet=Tweet::getTweet($id);
        $tweet=Tweet::find('id',$id);
        //var_dump($tweet);
        if($tweet==null)
            return null;
        
        $time=new DateTime("now");
        $tweet[0]->content=$content;
        $tweet[0]->updated_at=$time->format('Y-m-d H:i:s');
        $tweet[0]->save();    
        return true;
    }

   
    //*********************************STATIC FUNCTION************************ */
    //++++++++++++++++++++++++++++++++GET FUNCTION++++++++++++++++++++++++++++++
    
    public function user(){
        //var_dump($_SESSION['user_log_id']);
        if(empty($_SESSION['user_log_id']))
            return Response::json(AuthController::NotLogged,Response::HTTP_UNAUTHORIZED);
        $data=[
            'id'=> $this->session[0]->user_id,
            'username'=> $this->session[0]->user()->username,
            'email' => $this->session[0]->user()->email
        ];
        return Response::json($data);
    }
}