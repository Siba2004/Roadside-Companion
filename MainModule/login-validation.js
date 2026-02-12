 function validate(e){
        let error=false;
        let form=document.getElementById('loginForm');
        let login_id=form.elements['login_id'].value
        let password=form.elements['password'].value

        let login_idError=document.getElementById('login_idError');
        let passwordError=document.getElementById('passwordError');
        
        if(login_id===""){
            login_idError.innerHTML="Please enter your email or phone number"
            error=true
        }else{
            login_idError.innerHTML=""
        }
        let passErrMsg="";
        if(password === ""){
            passErrMsg +="Password is required<br>"
            error = true
        }
        if(passErrMsg === ""){
            passwordError.innerHTML = ""
        } else {
            passwordError.innerHTML = passErrMsg
        }
        if(error){
            e.preventDefault();
        }
}