function validate(e){
    let error=false;

    let form=document.getElementById("regForm");
    let name=form.elements['fullname'].value
    let email=form.elements['email'].value
    let phone=form.elements['phone'].value
    let password=form.elements['password'].value
    let cpassword=form.elements['cpassword'].value
    let account=form.elements['account'].value

    let nameError=document.getElementById("nameError")
    let emailError=document.getElementById("emailError")
    let phoneError=document.getElementById("phoneError")
    let passError=document.getElementById("passError")
    let cpassError=document.getElementById("cpassError")
    let accountError=document.getElementById("accountError")

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
        phoneError.innerHTML="Mobile is required"
        error=true
    }else if(!phoneRegx.test(phone)){
        phoneError.innerHTML="Please enter a 10 digit valid mobile number"
        error=true
    }else{
        phoneError.innerHTML=""
    }

    if(account===""){
        accountError.innerHTML="Account type is required"
        error=true
    }else{
        accountError.innerHTML=""
    }

    let passErrMsg=""
    if(password===""){
        passErrMsg+="Password is required<br>"
        error=true
    }if(!/[a-z]/.test(password)){
        passErrMsg+="Password should have 1 loswe case character<br>"
        error=true
    }if(!/[A-Z]/.test(password)){
        passErrMsg+="Password should have 1 upper case character<br>"
        error=true
    }if(!/[0-9]/.test(password)){
        passErrMsg+="Password should have 1 number<br>"
        error=true
    }if(!/[@#$%^&]/.test(password)){
        passErrMsg+="Password should have 1 special character<br>"
        error=true
    }if(password.length<8 || password.length >15){
        passErrMsg+="Password length should be between 8 -15<br>"
        error=true
    }

    if(passErrMsg===""){
        passError.innerHTML=""
    }else{
        passError.innerHTML=passErrMsg;
        error=true
    }

    if(cpassword!==password){
        cpassError.innerHTML="check the confirm password"
        error=true
    }else{
        cpassError.innerHTML=""
    }

    if(error){
        e.preventDefault();
        return false;
    }
    return true;
}