<?php 
require_once( __DIR__. '/../../Models/User.php');
require_once( __DIR__. '/../../Models/Session.php');
require_once( __DIR__. '/../../Models/Tweet.php');
require_once( __DIR__. '/../../Models/LikeTable.php');
require_once( __DIR__. '/../../Models/TweetComment.php');
require_once('AuthController.php');

class PublicController extends Model{

    static function getTweet($id){
        $tweet=Tweet::find('id',$id);
        
        if(!isset($tweet))
            return null;
        return $tweet;
    }

    static function TweetComments($id){
        
        $tweetComment=TweetComment::find('id',$id);
        if(!isset($tweetComment))
            return null;
        
        return $tweetComment;
    }

    static function getUser($id){
        $username=User::find('id',$id);
        if(!isset($username))
            return Response::json(['message'=>"no user found"],Response::HTTP_BAD_REQUEST);
        
        return $username;
    }
    static function userTweet($id){
        $user = User::find('id',$id);
        return $user->tweets();

    }
    static function getTweetByTweetComments($id){
        $tweet=TweetComment::find('tweet_id',$id);
        if(!isset($tweet))
            return null;
        
        return $tweet;
    }

    //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    public function getUserByTweet(Request $request, $id){
        $tweet=PublicController::getTweet($id); 
        return Response::json($tweet[0]->user());
    }

    /*public function getUserByTweetComment(Request $request, $id){
        $tweetComment=TweetController::TweetComments($id);
        return Response::json($tweetComment[0]->user());
    }*/

    public function getTweetComments(Request $request, $id){
        $tweet = PublicController::getTweet($id);
        $comments = $tweet[0]->comments();
        $data = new ArrayObject();
        //var_dump($comments);

        foreach($comments as $comment){
            $user=$comment->user();
            $userANDcomment=[
                'comment' => $comment,
                'user' => ['username' => $user->username,
                            'id' => $user->id]
            ];
            $data[]=$userANDcomment; 
        }
        return Response::json(new ArrayObject(array_reverse($data->getArrayCopy())));
    }

    public function getAllTweets(Request $request){
        $tweets =Tweet::all();
        $data = new ArrayObject();
        
        $session = Session::getSession();
        foreach($tweets as $tweet){
            $likes=LikeTable::whereRaw('tweet_id = '.$tweet->id)->get();
            $user=$tweet->user();
            $userANDtweet =[
                'tweet' => $tweet,
                'user' => ['username' => $user->username,
                            'id' => $user->id],
                'likes' => count($likes),
                'user_like' => $session == null ? []: LikeTable::whereRaw('tweet_id = '. $tweet->id .' AND user_id = '.$session[0]->user_id)->get()
            ];
            $data[] = $userANDtweet;
        
        }
        
        return Response::json( new ArrayObject(array_reverse($data->getArrayCopy())));
    }
    public function getUserTweet($id){
        return Response::json(PublicController::userTweet($id));
    }
}