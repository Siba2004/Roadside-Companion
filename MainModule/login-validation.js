function validate(){
        let error=false;
        let form=document.getElementById('regForm')
        let usertype=form.elements['usertype'].value
        let login_id=form.elements['login_id'].value.trim()
        let password=form.elements['password'].value

        let usertypeError=document.getElementById('usertypeError')
        let login_idError=document.getElementById('login_idError')
        let passwordError=document.getElementById('passwordError')
        
        usertypeError.innerHTML = ""
        login_idError.innerHTML = ""
        passwordError.innerHTML = ""
        
        if(usertype === "" || usertype === null){
            usertypeError.innerHTML="Please select an account type"
            error=true
        }

        if(login_id === ""){
            login_idError.innerHTML = "Please enter your email or phone number"
            error = true;
        }else{
            let emailRegex = /^[a-z0-9_\.]{3,}@[a-z0-9\.]{3,15}\.[a-z]{2,5}$/
            let phoneRegex =/^[6-9][0-9]{9}$/

            if(emailRegex.test(login_id)){
                login_idError.innerHTML = ""

            }else if(phoneRegex.test(login_id)){
                login_idError.innerHTML = ""

            }else{
                login_idError.innerHTML = "Enter a valid Email or 10 digit Phone number"
                error = true
            }
        }

        if(password===""){
            passwordError.innerHTML+="Password is required<br>"
            error=true
        }if(!/[a-z]/.test(password)){
            passwordError.innerHTML+="Password should have 1 loswe case character<br>"
            error=true
        }if(!/[A-Z]/.test(password)){
            passwordError.innerHTML+="Password should have 1 upper case character<br>"
            error=true
        }if(!/[0-9]/.test(password)){
            passwordError.innerHTML+="Password should have 1 number<br>"
            error=true
        }if(!/[@#$%^&]/.test(password)){
            passwordError.innerHTML+="Password should have 1 special character<br>"
            error=true
        }if(password.length <6 || password.length >15){
            passwordError.innerHTML+="Password length should be between 6-15<br>"
            error=true
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
