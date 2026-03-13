function validate(){
        let error=false;
        let form=document.getElementById('regForm');
        let usertype=form.elements['usertype'].value
        let login_id=form.elements['login_id'].value.trim();
        let password=form.elements['password'].value

        let usertypeError=document.getElementById('usertypeError');
        let login_idError=document.getElementById('login_idError');
        let passwordError=document.getElementById('passwordError');
        
        usertypeError.innerHTML = "";
        login_idError.innerHTML = "";
        passwordError.innerHTML = "";
        
        if(usertype === "" || usertype === null){
            usertypeError.innerHTML="Please select an account type"
            error=true
        }

        if(login_id===""){
            login_idError.innerHTML="Please enter your email or phone number"
            error=true
        }else{
            login_idError.innerHTML=""
        }

        if(password === ""){
            passwordError.innerHTML="Password is required"
            error = true
        }else if(password.length < 6){
            passwordError.innerHTML="Password must be at least 6 characters long"
            error = true
        }else{
            passwordError.innerHTML=""
        }
        
        if(error){
            return false;
        }
        return true;
}

function togglePassword(){
    let pass = document.getElementById("password");
    let eye = document.getElementById("eye");

    if(pass.type === "password"){
        pass.type="text";
        eye.classList.remove("bi-eye-slash");
        eye.classList.add("bi-eye");
    }
    else{
        pass.type="password";
        eye.classList.remove("bi-eye");
        eye.classList.add("bi-eye-slash");
    }

}
