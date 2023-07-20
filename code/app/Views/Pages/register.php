<!DOCTYPE html>
<html>
<head>
  <title>Form di Registrazione</title>
</head>
<body>
  <h2>Registrati</h2>
  <form id="registrationForm" onsubmit="return validateForm()" method="post" >
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <br>
    <label for="email">Email:</label>
    <input type="text" id="email" name="email" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <label for="confirm_password">Conferma Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" required>
    <br>
    <input type="submit" value="Registrati">
  </form>
</body>
</html>
<script>
    
    async function sendDataToServer(formData) {
      try {
        const url = 'http://127.0.0.1/api/auth/register'; 
        const response = await fetch(url, {
          method: 'POST',
          body: formData,
        });
        console.log(response);
        if (!response.ok) {
          const responseData = await response.json();
          throw new Error(responseData);
        }else{
          window.location.assign("http://127.0.0.1/auth/loginView");
        }
        // Aggiungi qui le operazioni da fare con la risposta del server

      } catch (error) {
        console.log('Errore:', error);
        alert(error);
      }
    }

    async function validateForm(){
        event.preventDefault();
        const form = document.getElementById('registrationForm');
        const formData = new FormData(form);
        await sendDataToServer(formData);
    }

</script>
