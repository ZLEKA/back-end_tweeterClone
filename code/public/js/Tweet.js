class Tweet {
    #user;
    constructor(id=null, content=""){
        this.#user = new User(); 
        if(!id){
            this.id=id;
            this.tweet=document.getElementById(id);
        }else{
            this.tweet = document.createElement('div');
            const newSpan = document.createElement('span');

            //console.log(tweet);
            newSpan.textContent = this.user.username+" : "+content;
            newSpan.setAttribute('id','text');
            this.tweet.appendChild(newSpan);
        }
    }
    setId(id){
        this.tweet.setAttribute('id',id);
    }
    addCssClass(cssClassTweet){
        this.tweet.classList.add(cssClassTweet);
    }
    updateContent(content){
        

    }

}