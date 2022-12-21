export default class Gate{

    constructor(user){
        this.user = user;
    }

    isAdmin(){
        return this.user.type === 'admin';
    }

    isUser(){
        return this.user.type === 'user';
    }

    isKithcenUser(){
        return this.user.type === 'kithcen';
    }
    
    isParcelUser(){
        return this.user.type === 'parcel';
    }

    isFinanceUser(){
        return this.user.type === 'finance';
    }

    isAdminOrUser(){
        if(this.user.type === 'user' || this.user.type === 'admin'){
            return true;
        }
    }
}

