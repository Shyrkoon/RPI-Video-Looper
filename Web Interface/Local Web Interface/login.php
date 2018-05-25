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
		<title>Login Web Interface</title>
		<link rel="icon" type="image/png" href="images/logo.png">
		<link rel="stylesheet" href="w3.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src='https://www.google.com/recaptcha/api.js'></script>
	</head>
	<body class="backgroundWeb">
		<header class="w3-hide-small w3-teal w3-dark-grey w3-row">
			<h1 class="w3-animate-left w3-container"> Login RPI Video Projecte <i class="glyphicon glyphicon-user"></i></h1>
		</header>
		<!-- Formulari Login -->
		<div class="vertical-center">
			<div class="w3-container w3-third w3-center w3-grey-shadow-shadow customShadow" style="position:relative;">
				<form action='dbConnection.php' method='post'>
					<label><b><i class="glyphicon glyphicon-user w3-margin-top"></i> Usuari</b></label></br>
					<input class="form-control w3-round-large w3-margin-top w3-text-black" type='text' name='user' placeholder='Introdueix el usuari' required></br>
					<label><b><i class="glyphicon glyphicon-lock"></i> Password</b></label></br>
					<input class="form-control w3-round-large w3-margin-top w3-text-black" type='password' name='pass' placeholder='Introdueix la contrasenya' required></br>
					<br>
					<button id="button1" class="btn w3-block w3-purple w3-hover-shadow w3-hover-text-white w3-round-large nounderline w3-margin-bottom" type='submit' name='submitbuttonL'>Envia</button>
					<p class="w3-margin-top w3-text-deep-purple">No tens compte?</p>
					<a href="register.php" class="btn w3-block w3-purple w3-hover-shadow w3-hover-text-white w3-round-large nounderline w3-margin-bottom">
						<p>Registrar-se</p>
					</a>
					<!-- Alerta credencials malaments -->
					<div class="w3-panel w3-red w3-display-container w3-card-4 <?php echo hideLoginError(); ?>">
						<h3>Error!</h3>
						<p>Has posat malament les credencials.</p>
					</div>
				</form>
				<div class="w3-display-bottom-middle w3-center w3-margin-top w3-margin-bottom">
					<a class="w3-text-white nounderline" href="legal.html">Avís Legal</a>
				</div>
			</div>
		</div>
	</body>
</html>
