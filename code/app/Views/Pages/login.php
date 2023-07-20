<!DOCTYPE html>
<html>
<head>
  <title>Form di login</title>
</head>
<body>
  <h2>Accedi</h2>
  <form id="loginForm" onsubmit="return validateForm()" method="post" >
    <label for="email">Email:</label>
    <input type="text" id="email" name="email" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    
    <input type="submit" value="Accedi">
  </form>
</body>
</html>
<script>
    
    async function sendDataToServer(formData) {
      try {
        const url = 'http://127.0.0.1/api/auth/login'; 
        const response = await fetch(url, {
          method: 'POST',
          body: formData,
        });
        if (!response.ok){
          window.location.assign("http://127.0.0.1/auth/registerView");
        }else{
          window.location.assign("http://127.0.0.1/tweet/dashboard");
        }
        // Aggiungi qui le operazioni da fare con la risposta del server

      } catch (error) {
        console.log('Errore:', error);
        
      }
    }

    async function validateForm(){
        event.preventDefault();
        const form = document.getElementById('loginForm');
        const formData = new FormData(form);
        await sendDataToServer( formData );
    }
    
</script>
