class User {
    #user;
    constructor(){
        getUser();
    }
    async getUser(){
        this.#user = await askToServerGet(API_REQUEST+'user');
        setTimeout(getUser,3600000);
    }
    
    isLog(){
        return this.#user ? true:false;
    }
    userName(){
        return this.#user.username;
    }
}