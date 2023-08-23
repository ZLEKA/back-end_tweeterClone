<!DOCTYPE html>
<html>

<head>
  <title>Form di Registrazione</title>
  <link rel="stylesheet" type="text/css" href="/../../../public/css/style.css">
  <script src="/../../../public/js/script.js"></script>
</head>

<body>
  <h2>Registrati</h2>
  <form id="registrationForm" method="post">
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
    <input id='register' type="submit" value="Registrati">
  </form>
</body>

</html>
<script>
  const registerButton = document.getElementById('register');
  registerButton.addEventListener('click', async function(event) {
    await validateForm();
  });
  const validateForm = async () => {
    event.preventDefault();
    const form = document.getElementById('registrationForm');
    const formData = new FormData(form);
    const response = await sendDataToServer(formData, "/api/auth/register");
    console.log(response);
    if (response!=null) {
      window.location = "http://127.0.0.1/auth/loginView";
    } else {
      alert("something go wrong plese retry");
    }
  }
</script>