<!DOCTYPE html>
<html>

<head>
  <title>Dashboard</title>
  <link rel="stylesheet" type="text/css" href="/../../../public/css/style.css">
  <script src="/../../../public/js/script.js"></script>
</head>

<body>
  <h2 id='hello-home'></h2>
  
  <input id="logout" type="submit" class='custom-btn btn-3 right' value="Esci">
  <div id='modal' class="modal center">
    
    <div class="modal-content center">
        <input type="text" id="comment-content">
    </div>
    <input type='submit' value="commenta" id='buttonCommentModel' class='custom-btn commenta center'>
  </div>
  <form id="dashboardFrom" method="post">
    <div id='newTweet' class='newTweet'>
      <textarea id='new-tweet' name='text' wrap='soft' class='input'></textarea>
    </div>
    <div class="center">
      <input type="submit" id="new-tweet-submit" class="custom-btn btn-3" value="manda tweet">
    </div>
    <div id='tweet' class='all-inside'>
      <div class='container'>
        <div id='myTweets' class='flex-container'>
        </div>
      </div>
      <div id="allTweet" class='flex-center-public'>
      </div>
    </div>
    
  </form>
</body>

</html>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    userNameDash('hello-home');
    makePage();

    const form = document.getElementById('dashboardFrom');
    const submitNewTweet = document.getElementById('new-tweet-submit');
    const submitLogout = document.getElementById('logout');
    const buttonModal = document.getElementById('buttonCommentModel');
    buttonModal.addEventListener('click',async function(){
          const commentContent = document.getElementById('comment-content');
          const commentContentText = commentContent.value || commentContent.textContent;
          const tweet = document.getElementById('modal');
          const idTweet = tweet.getAttribute('id-tweet');
          console.log(idTweet);
          if(commentContentText==""){
            return;
          }
          if( tweet.getAttribute('comment')=="1"){
            const form = new FormData();
            form.append('tweet_id', idTweet);
            form.append("content", commentContentText);
            const reciveData = await sendDataToServer(form, 'http://127.0.0.1/api/tweet/comment')
            if (reciveData.content === null) {
              alert("something go wrong");
              return;
            }
            console.log(reciveData);
            commentContent.value="";
            
          

          const divModal = document.getElementById('modal');
          divModal.classList.remove('display');
          const myTweets = document.getElementById('myTweets');
          console.log(myTweets);
          const myTweet = myTweets.querySelector('[id="'+idTweet+'"]');
          
          console.log(myTweet);
          const myTweetChildren =  myTweet.children;
          console.log(myTweetChildren);
          const arrayMyTweetChildren =Array.from(myTweetChildren);
          arrayMyTweetChildren.forEach(child => {
            if(child.id!=null){
              myTweet.insertBefore(commentContent,child);
              console.log(child);
              return ;
            }
          });
          tweet.setAttribute('comment', '0');
        }else if(tweet.getAttribute('update')=="1"){
          
          tweet.setAttribute('update', '0');
        }
         }); 


    submitNewTweet.addEventListener('click', async function(event) {
      event.preventDefault();
      const newtweet = document.getElementById('new-tweet');
      const contentText = newtweet.value || newtweet.textContent;
     /* const data = {
        content: contentText
      };
      console.log(data);*/
      const form = new FormData();
      form.append("content", contentText);
      const reciveData = await sendDataToServer(form, 'http://127.0.0.1/api/tweet')
      if (reciveData.content === null) {
        alert("something go wrong");
        return;
      }
      newtweet.value = "";
      const newTweet = await buildTweet(reciveData, 'flex-container' );
      const divMyTweet = document.getElementById('myTweets');
      const divAllTweet = document.getElementById('allTweet');
      const myfirstChild = divMyTweet.firstElementChild;
      const allfirstChild = divAllTweet.firstElementChild;

      divMyTweet.insertBefore(newTweet, myfirstChild);
      divAllTweet.insertBefore(newTweet, allfirstChild);
    });
    submitLogout.addEventListener('click', function(event) {
      event.preventDefault();
      sendDataToServer('', 'http://127.0.0.1/api/auth/logout');
      location.href="http://127.0.0.1/auth/loginView";
    });
  });
</script>