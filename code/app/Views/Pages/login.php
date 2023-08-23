<!DOCTYPE html>
<html>
<head>
  <title>Form di login</title>
  <link rel="stylesheet" type="text/css" href="/../../../public/css/style.css">
  <script src="/../../../public/js/script.js"></script>
</head>
<body>
  <h2>Accedi</h2>
  <form id="loginForm"  method="post" >
    <label for="email">Email:</label>
    <input type="text" id="email" name="email" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    
    <input id='login' class='custom-btn  btn-3' type="submit" value="Accedi">
    <input id='register' class='custom-btn  btn-3' type="submit" value="Registrati">
  </form>
</body>
</html>
<script>
const loginButton = document.getElementById('login');
loginButton.addEventListener('click', async function(event) {
  validateForm();
});
const registerButton = document.getElementById('register');
registerButton.addEventListener('click', function(event){
  window.location="http://127.0.0.1/auth/registerView";
});

const validateForm = async()=>{
        event.preventDefault();
        const form = document.getElementById('loginForm');
        const formData = new FormData(form);
        console.log(formData);
        const response = await sendDataToServer( formData ,'http://127.0.0.1/api/auth/login');
        if(response.ok) 
          window.location="http://127.0.0.1/tweet/dashboard";
        
        alert("Something go wrong ");
        
    }
    
</script>
