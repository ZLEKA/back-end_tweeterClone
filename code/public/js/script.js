
const urlUserByTweet='/api/tweet/getUserByTweet/';
const urlCommentByTweet='/api/tweet/getTweetComments/';
const urlUsernameByTweetComment='/api/tweet/getUserByTweetComment/'
const API_REQUEST ='http://127.0.0.1/api/tweet/';

const isLog = async() => {
  return await askToServerGet(API_REQUEST+'user');
}
let logUser = setInterval(isLog, 3600000);//evry hour
//++++++++++++++++++++++++++++++++++++++++++++++++//
const sendDataToServer= async(formData, url, method='POST') => {
    try {
       
      const response = await fetch(url, {
        method: method,
        body: formData,
      });
      const data = await response.json();
      if (!response.ok) {
        console.log(response);
        return null;
        //throw new Error(response);
      }
      console.log(response);
      return data;
  

    } catch (error) {
      console.log('Errore:', error);
      return null;
    }
  }

/**
 * 
 * @param {*} url 
 * @returns 
 */
const askToServerGet = async(url) => {
    try {
        const response = await fetch(url);
        const data = await response.json();
        if (!response.ok) {
          //  throw new Error(response);
          return null;
        }
        return data;
        } catch (error) {
        //console.log('Errore:', error);   
    }
}



//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++//
const makePage = async() => {
    logUser = await askToServerGet(API_REQUEST+'user');
    //console.log(logUser);
    const urlTweets = API_REQUEST+'getAllTweets'; 
    const tweetsANDuser = await askToServerGet(urlTweets);
    buildTweets(tweetsANDuser, 'allTweet', 'flex-container','flex-container-comment' );
  
}

/**
 * build all tweet in with their div 
 * also add button comment e if it was write by logged user he can modify it
 * @param {objectTweet} tweets 
 * @param {divId} elmById 
 * @param {CssClassTweet} cssClassTweet 
 * @param {CssClassTweetComment} cssClassComment 
 */
const buildTweets = async(tweets, elmById , cssClassTweet, cssClassComment) => {
  let divBox=document.getElementById(elmById);
  for(const tweet in tweets){  
    const firstChild = divBox.firstElementChild;
    const newDiv = await buildTweet(tweets[tweet], cssClassTweet);  
    newDiv.setAttribute('id',tweets[tweet]['tweet'].id);
    divBox.insertBefore(newDiv,firstChild);
    
    addCommenUpdateShowcomment(newDiv, tweets[tweet]);
  }
}

const addCommenUpdateShowcomment = async(newDiv, tweet) => {
    
    const buttonComm = makeButton('pulse', 'commenta');
    const divModal = document.getElementById('modal');
    const buttonModal = document.getElementById('buttonModel');
    
    
 
    const buttonShowcomment = makeButton('pulse', 'mostra commenti');
    buttonShowcomment.setAttribute('id-tweet', tweet['tweet'].id );
    buttonShowcomment.setAttribute('show', "0");
    const commentsDiv = document.createElement('div');
    let commetsArrayTrack = {};
    if(commetsArrayTrack[tweet['tweet'].id] == null) commetsArrayTrack[tweet['tweet'].id]=0;
    buttonShowcomment.addEventListener('click', async function(event){
      event.preventDefault();
      
      const whatTweet = document.getElementById(buttonShowcomment.getAttribute('id-tweet'));
      //console.log(whatTweet);
      console.log(buttonShowcomment);
      const comments = await askToServerGet(API_REQUEST+'getTweetComments/'+tweet['tweet'].id);
      //console.log(commentsDiv);
      let firstChild = commentsDiv.firstChild;
      console.log(firstChild);
      
      
      if(Object.keys(comments).length === 0 && commentsDiv.childNodes.length == 0){
        const data ={
          user:{
            username:""
        },
          comment:{
            content:"no comments for now"
          }
        };
        const comment = buildComment(data,'flex-container-comment');
        comment.setAttribute('id','empty-'+tweet['tweet'].id);
        commentsDiv.appendChild(comment);

      }else if (commentsDiv.childNodes.length == 0 || commetsArrayTrack[tweet['tweet'].id]!=Object.keys(comments).length){
        //console.log(firstChild);
        //const comment = document.getElementById('empty-'+tweet['tweet'].id);
         console.log(commentsDiv);
        commentsDiv.innerHTML="";

        let count = 0;
        for(const comment in comments){
          console.log(firstChild);
          if(firstChild == null) {
            commentsDiv.appendChild(buildComment(comments[comment], 'flex-container-comment'));
            firstChild = commentsDiv.firstChild;
          }else {
            firstChild = commentsDiv.firstChild;
            commentsDiv.insertBefore(buildComment(comments[comment],'flex-container-comment'),firstChild);
            
            }
          firstChild = commentsDiv.firstChild;
          count++;

        }
        commetsArrayTrack[tweet['tweet'].id]=Object.keys(comments).length;
      }
      commentsDiv.setAttribute('id-tweet-comments', tweet['tweet'].id);
      console.log(whatTweet)
      whatTweet.appendChild(commentsDiv); 
      if(buttonShowcomment.getAttribute('show') == "0"){
        buttonShowcomment.setAttribute('show',"1");
        commentsDiv.classList.remove('not-display');
      }else{
        buttonShowcomment.setAttribute('show', '0');
        commentsDiv.classList.add('not-display');
      }

    });
    if(logUser){
    buttonComm.addEventListener('click', function(event){
      event.preventDefault();
      bottonInizialize(tweet['tweet'].id);
      buttonModal.setAttribute('value','commenta');
        divModal.setAttribute('operation', 'comment' );    
    });
   

    if(tweet['user'].username == logUser.username ){
      const buttonUpdate = makeButton('pulse', 'modifica');
      buttonUpdate.setAttribute('id-tweet', tweet['tweet'].id );
      buttonUpdate.addEventListener('click', function(event){
        event.preventDefault();
        buttonModal.setAttribute('value','aggirona');
        bottonInizialize(tweet['tweet'].id);
        divModal.setAttribute('operation', 'update' );
      });
      
      newDiv.appendChild(buttonUpdate);

      //+++++++++++++++++++++++++++++++++delete+++++++++++++++++++++++++++++++++++++
      const buttonDelete =  makeButton('pulse', 'cancella');
      buttonDelete.setAttribute('id-tweet', tweet['tweet'].id );
      buttonDelete.addEventListener('click', async function(event){  
      event.preventDefault();
      //bottonInizialize(tweet['tweet'].id);  
      const form = new FormData(); 
      const deleteDiv = await sendDataToServer(form, API_REQUEST+'delete/'+tweet['tweet'].id, 'DELETE');
      const parentDiv = document.getElementById('allTweet');
      const removeDiv = document.getElementById(tweet['tweet'].id);

      parentDiv.removeChild(removeDiv);
      
      });
    newDiv.appendChild(buttonDelete);
    }
      newDiv.appendChild(buttonComm);
      //newDiv.appendChild(buttonDelete)
    }
      let firstChild = newDiv.firstChildElementChild;
      newDiv.insertBefore(buttonShowcomment, firstChild);
      
      
    
}

const bottonInizialize = (tweetId)=>{
        const divModal = document.getElementById('modal');
        //console.log(divModal, 'sono vivo');
        divModal.classList.add('display');
        divModal.setAttribute('id-tweet', tweetId );
}

const buildTweet = async(tweet,cssClassTweet) => {
  const newDiv = document.createElement('div');
  const newSpan = document.createElement('span');
  console.log(tweet);
  newSpan.textContent = tweet['user'].username+" : "+tweet['tweet'].content;
  newSpan.setAttribute('id',' #text');
  newDiv.appendChild(newSpan);
  newDiv.classList.add(cssClassTweet);
  return newDiv;
}

const makeButton = (cssClassButton, buttonContent) => {
  const newButton=document.createElement('button');
  newButton.innerText=buttonContent;
  newButton.classList.add(cssClassButton);
  return newButton;
} 


/**
 * build a comment by passing comment e his css class
 * @param {*} comment 
 * @param {*} cssClassComment 
 * @returns 
 */

const buildComment = (comment, cssClassComment)=>{ 
    const newCommentDiv = document.createElement('div');
    //console.log(comment);
    newCommentDiv.textContent = comment['user'].username+" : "+comment['comment'].content;
    newCommentDiv.classList.add(cssClassComment);
    newCommentDiv.setAttribute('id', 'comment-'+comment['comment'].id);
    return newCommentDiv;
} 

