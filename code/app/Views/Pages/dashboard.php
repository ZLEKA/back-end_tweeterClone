<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
</head>
<body>
  <h2>Dashboard</h2>
    <form id="loginForm" onsubmit="return validateForm()" method="post" >

    
    <input type="submit" value="Esci">
  </form>
</body>
</html>
<script>
    
    async function sendDataToServer(formData) {
      try {
        const url = 'http://127.0.0.1/auth/logout'; // Sostituisci con l'URL dell'endpoint della tua API
        const response = await fetch(url, {
          method: 'POST',
          body: formData,
        });
        if (!response.ok) {
          throw new Error(response);
        }
        window.location.assign("http://127.0.0.1/");
        
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