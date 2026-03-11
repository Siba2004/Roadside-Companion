AOS.init({ duration: 1000 });
function validate(e){
        let error=false;
        let form=document.getElementById('regForm');
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
            return false;
        }
        return true;
}
function switchLoginOption(option) {
            const emailOption = document.getElementById('emailOption');
            const phoneOption = document.getElementById('phoneOption');
            const emailField = document.getElementById('emailField');
            const phoneField = document.getElementById('phoneField');
            
            if (option === 'email') {
                emailOption.classList.add('active');
                phoneOption.classList.remove('active');
                emailField.style.display = 'block';
                phoneField.style.display = 'none';
            } else {
                phoneOption.classList.add('active');
                emailOption.classList.remove('active');
                phoneField.style.display = 'block';
                emailField.style.display = 'none';
            }
        }
function togglePassword() {
            const passwordField = document.getElementById('passwordField');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
document.querySelector('input[type="tel"]')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9+]/g, '');
        });