function validate(){
    let error=false;

    let form=document.getElementById("regForm");
    let name=form.elements['name'].value
    let email=form.elements['email'].value
    let phone=form.elements['phone'].value
    let password=form.elements['password'].value
    let cpassword=form.elements['confirmPassword'].value
    let account=form.elements['account'].value

    let nameError=document.getElementById("usernameError")
    let emailError=document.getElementById("emailError")
    let phoneError=document.getElementById("phoneError")
    let passError=document.getElementById("passwordError")
    let cpassError=document.getElementById("confirmPasswordError")
    let accountError=document.getElementById("accountError")

    nameError.innerHTML="";
    emailError.innerHTML="";
    phoneError.innerHTML="";
    passError.innerHTML="";
    cpassError.innerHTML="";
    accountError.innerHTML="";

    if(name===""){
        nameError.innerHTML="Name is require"
        error= true
    }else{
        nameError.innerHTML=""
    }

    let emailRegx=/^[a-z0-9_\.]{3,}@[a-z0-9\.]{3,15}\.[a-z]{2,5}$/
    if(email===""){
        emailError.innerHTML="Email is required"
        error=true
    }else if(!emailRegx.test(email)){
        emailError.innerHTML="please enter a valid email"
        error=true
    }else{
        emailError.innerHTML=""
    }

    let phoneRegx=/^[6-9][0-9]{9}$/
    if(phone===""){
        phoneError.innerHTML="Phone number is required"
        error=true
    }else if(!phoneRegx.test(phone)){
        phoneError.innerHTML="Please enter a 10 digit valid phone number"
        error=true
    }else if(phone.length<10){
        phoneError.innerHTML="Phone number must be 10 digits"
        error=true
    }else{
        phoneError.innerHTML=""
    }

    if(account==="" || account===null){
        accountError.innerHTML="Account type is required"
        error=true
    }else{
        accountError.innerHTML=""
    }

    if(password===""){
        passError.innerHTML+="Password is required<br>"
        error=true
    }if(!/[a-z]/.test(password)){
        passError.innerHTML+="Password should have 1 loswe case character<br>"
        error=true
    }if(!/[A-Z]/.test(password)){
        passError.innerHTML+="Password should have 1 upper case character<br>"
        error=true
    }if(!/[0-9]/.test(password)){
        passError.innerHTML+="Password should have 1 number<br>"
        error=true
    }if(!/[@#$%^&]/.test(password)){
        passError.innerHTML+="Password should have 1 special character<br>"
        error=true
    }if(password.length <6 || password.length >15){
        passError.innerHTML+="Password length should be between 6-15<br>"
        error=true
    }

    if(cpassword===""){
        cpassError.innerHTML="Confirm password is required"
        error=true
    }else if(cpassword!==password){
        cpassError.innerHTML="check the confirm password"
        error=true
    }else{
        cpassError.innerHTML=""
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

function toggleConfirmPassword(){
    let pass = document.getElementById("confirmPassword");
    let eye = document.getElementById("eye2");

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
