<!DOCTYPE html>
<html>

<head>
  <title>Dashboard</title>
  <link rel="stylesheet" type="text/css" href="/../../../public/css/style.css">
  <script src="/../../../public/js/script.js"></script>
</head>

<body>
  <h2 id='hello-home'>Benvenuto </h2>
  
  <input id="logout" type="submit" class='custom-btn btn-3 right ' value="Esci">
  <div id='modal' class="modal center ">
    
    <div class="modal-content center ">
        <input type="text" id="comment-content">
    </div>
    <input type='submit' value="" id='buttonModel' class='custom-btn commenta center '>
  </div>
  <form id="dashboardFrom" method="post">
    <div id='tweet-hidden' class='not-display'>
      <div id='newTweet' class='newTweet'>
        <textarea id='new-tweet' name='text' wrap='soft' class='input'></textarea>
      </div>
      <div class="center">
        <input type="submit" id="new-tweet-submit" class="custom-btn btn-3 " value="manda tweet">
      </div>
    </div>
    <div id='tweet' class='all-inside'>
      
      <div id="allTweet" class='flex-center-public'>
      </div>
    </div>
    
  </form>
</body>

</html>
<script>
  document.addEventListener('DOMContentLoaded', async function() {
    const home =document.getElementById('hello-home');
    
    await makePage();
    
    const form = document.getElementById('dashboardFrom');
    const submitNewTweet = document.getElementById('new-tweet-submit');
    const hidden =document.getElementById('tweet-hidden');
    const submitLog = document.getElementById('logout');
    const buttonModal = document.getElementById('buttonModel');
    const modal = document.getElementById('modal');
    if(logUser){
      home.textContent="Benvenuto "+logUser['username'];
      hidden.classList.remove('not-display');
    buttonModal.addEventListener('click',async function(){
          const modelContent = document.getElementById('comment-content');
          const modelContentText = modelContent.value || modelContent.textContent;
          modelContent.value="";
          
          const idTweet = modal.getAttribute('id-tweet');
          console.log(idTweet);
          console.log(modelContent);
          const form = new FormData();
          form.append('tweet_id', idTweet);
          form.append('content', modelContentText);
          console.log(form);
          if(modelContentText == ""){
            modal.classList.remove('display');
            return;
          }
          
          const divAllTweet = document.getElementById('allTweet');
          if( modal.getAttribute('operation')=="comment"){
            
            const reciveData = await sendDataToServer(form, 'http://127.0.0.1/api/tweet/comment');
            console.log(reciveData);
            if (reciveData.content === null) {
              alert("something go wrong");
              divModal.classList.remove('display');
              return;
            }
          }else{
            console.log("vivo");
            
            const reciveData = await sendDataToServer(form, 'http://127.0.0.1/api/tweet/update');
            console.log(reciveData);
            if (reciveData.content === null) {
              alert("something go wrong");
              divModal.classList.remove('display');
              return;
            }
            const tweetText = document.getElementById(idTweet).children[0];
            console.log(tweet);
            tweetText.innerHTML="";  
            tweetText.innerHTML=logUser.username+" : "+ modelContentText;
                        
          }
          const divModal = document.getElementById('modal');
          divModal.classList.remove('display');
          
        }); 


    submitNewTweet.addEventListener('click', async function(event) {
      event.preventDefault();
      const newtweet = document.getElementById('new-tweet');
      const contentText = newtweet.value || newtweet.textContent;

      const form = new FormData();
      form.append('content', contentText);
      //console.log(contentText);
      const reciveData = await sendDataToServer(form, 'http://127.0.0.1/api/tweet');
        console.log(reciveData);
      if (reciveData == null) {
        alert("something go wrong");
        return;
      }
      newtweet.value = "";
      const newTweet = await buildTweet(reciveData, 'flex-container' );
      newTweet.setAttribute('id',reciveData['tweet'].id);
      addCommenUpdateShowcomment(newTweet, reciveData);
      //const divMyTweet = document.getElementById('myTweets');
      const divAllTweet = document.getElementById('allTweet');
      //const myfirstChild = divMyTweet.firstElementChild;
      const allfirstChild = divAllTweet.firstElementChild;

      //divMyTweet.insertBefore(newTweet, myfirstChild);
      divAllTweet.insertBefore(newTweet, allfirstChild);
    });
  }
  let redirect;
  if(!logUser){
    submitLog.value="Accedi";
    redirect="http://127.0.0.1/auth/loginView";
  }else {
    redirect="http://127.0.0.1/tweet/dashboard";
  }
  submitLog.addEventListener('click', function(event) {
      event.preventDefault();
      if(logUser)
        sendDataToServer('', 'http://127.0.0.1/api/auth/logout');
      
      location.href=redirect;
     

    });
  
  });
</script>