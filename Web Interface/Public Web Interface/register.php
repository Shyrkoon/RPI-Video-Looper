<?php
include 'dbConnection.php'; //connect the connection page
if(empty($_SESSION)) // if the session not yet started
   session_start();


if(isset($_SESSION['username'])) { // if already login
   header("location: home.php"); // send to home page
   exit();
}
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="w3.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body class="backgroundWeb">

    <!-- Header -->

    <header class="w3-container w3-teal w3-deep-purple w3-card-2">
        <h1 class="w3-animate-left w3-half">Registre RPI Video Projecte <i class="glyphicon glyphicon-pencil"></i></h1>
        <form action='login.php' method='post'>
          <h4><button class="w3-button w3-third w3-purple w3-hover-shadow w3-hover-text-white w3-round-large" type='submit' name='login' style="float: right;">Login</button></h4>
        </form>
    </header>

    <!-- Formulari Registre -->
    <div class="w3-container w3-center">
      <h3 class="w3-hide-small"><b>Omple les dades amb l'objectiu de registrarte al sistema, si ja estas registrat fes login.</b></h3>

      <div class="vertical-center">
        <div class="w3-container w3-purple w3-deep-purple">
        <form class="w3-margin" action='dbConnection.php' method='post' onsubmit="if(document.getElementById('agree').checked) { return true; } else { alert(' Si us plau, heu d\'acceptar els Termes i la Política de Privacitat. '); return false; }">
        <label><i class="glyphicon glyphicon-pencil"></i> <b>Nom</b></label></br>
        <input class="form-control w3-round-large w3-text-black" type='text' name='name' placeholder='Introdueix el nom' required></br>

        <label><i class="glyphicon glyphicon-pencil"></i> <b>Cognoms</b></label></br>
        <input class="form-control w3-round-large w3-margin-top w3-text-black" type='text' name='surnames' placeholder='Introdueix els cognoms' required></br>

        <label><i class="glyphicon glyphicon-envelope"></i> <b>E-mail</b></label></br>
        <input class="form-control w3-round-large w3-margin-top w3-text-black" type='email' name='email' placeholder='Introdueixi el correu' required></br>

        <label><i class="glyphicon glyphicon-user"></i> <b>Usuari</b></label></br>
        <input id="username" class="form-control w3-round-large w3-margin-top w3-text-black" type='text' name='user' placeholder='Introdueixi el usuari' required></br>
        <span id="result"></span></br>

        <label><i class="glyphicon glyphicon-lock"></i> <b>Password</b></label></br>
        <input id="password" class="form-control w3-round-large w3-margin-top w3-text-black" type='password' name='password' placeholder='Introdueixi la contrasenya' required></br>

        <label><i class="glyphicon glyphicon-lock"></i> <b>Confirmar Password</b></label></br>
        <input id="confirm_password" class="form-control w3-round-large w3-margin-top w3-text-black" type='password' name='confirm_password' placeholder='Confirmi la contrasenya' required onChange="checkPasswordMatch();"></br>

        <div id="message"></div>

        <input id="checkbox" type="checkbox" name="checkbox" value="check" id="agree" class="w3-margin-top" /> Ja he llegit i accepto els <a href="#">Termes i la Política de Privacitat.</a></br>

        <div class="w3-margin-top g-recaptcha w3-center" data-sitekey="6LcYNU4UAAAAALagWMvUhIrBfgZhgHcEZoN-mTXm" data-callback="enableBtn"></div><br>

        <button id="button1" class="w3-button w3-purple w3-hover-shadow w3-hover-text-white w3-round-large w3-large w3-margin-top" type='submit' name='submitbuttonR'>Envia</button>
        </form>
      </div>
    </div>
    </div>

    <!-- Avis Legal -->
    <footer class="footer-copyright py-3 text-center w3-margin">
        <a href="legal.html">Avís Legal</a>
    </footer>
    <!-- Script per desactivar el botó de submit si no es comprova que és un humà -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById("button1").disabled = true;
        }, false);
    </script>

    <!-- Script per activar el botó de submit si es comprova que és un humà -->
    <script>
        function enableBtn(){
            document.getElementById("button1").disabled = false;
        }
    </script>

  <script>
  $('#password, #confirm_password').on('keyup', function () {
      if ($('#password').val() == $('#confirm_password').val()) {
          $('#message').html('Correcte').css('color', 'white');
      } else
          $('#message').html('No Concideix la contrasenya').css('color', 'red');
  });
  </script>
  <script type="text/javascript">
		$(document).ready(function() {    
			$("#username").keyup(function() {		
				var name = $(this).val();
 
		if(name.length > 3)
			{		
				$("#result").html('checking...');
				$.ajax({
					type : 'POST',
					url  : 'checkUsername.php',
					data : {name:name},
					success : function(data) 
						{
					        $("#result").html(data);
					}
				});
					return false;
				} 
			else
		{
			$("#result").html('');
		}
	});	
});
</script>
</body>
</html>
