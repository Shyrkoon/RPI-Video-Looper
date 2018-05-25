<?php
	include "funcions.php";
	session_start();
	if(empty($_SESSION['username'])){
	    header("Location: /login.php"); /* Redirect browser */
	    exit();
	}
	elseif(empty(listRPI())){
	  header("Location: /registerRPI.php");
	}
	?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Descàrrega RPI Project</title>
		<link rel="icon" type="image/png" href="images/logo.png">
		<link rel="stylesheet" href="w3.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
	</head>
	<body class="w3-light-grey">
		<!--header-->
		<div>
			<header class="w3-row w3-teal w3-deep-purple w3-card-2">
				<h1 class="w3-animate-left">
					<div class="w3-teal" style="float:left;">
						<button class="w3-button w3-teal w3-xlarge" onclick="w3_open()">&#9776;</button>
					</div>
					<div style="float:left; margin-left: 10px; margin-bottom: 10px;">RPI Video Projecte <i class="glyphicon glyphicon-download-alt"></i></div>
				</h1>
			</header>
		</div>
		<!-- Sidebar -->
		<div class="w3-sidebar w3-bar-block" style="display:none" id="mySidebar">
			<button onclick="w3_close()" class="w3-bar-item w3-button w3-large w3-hover-text-white">Tancar &times;</button>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="home.php"><i class="glyphicon glyphicon-home"></i> Home</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="videos.php"><i class="glyphicon glyphicon-film"></i> Reproducció</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="#"><i class="glyphicon glyphicon-download-alt"></i> Descarrega</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="schedules.php"><i class="glyphicon glyphicon-calendar"></i> Horaris</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="logout.php"><i class="glyphicon glyphicon-log-out"></i> Tancar Sessió</a>
		</div>
		<!-- Page Content -->
		<div onclick="w3_close()">
		<div class="w3-container w3-margin w3-center">
			<div class="w3-container">
				<h2><i class="glyphicon glyphicon-hdd"></i> Espai ocupat del disc</h2>
				<div class="w3-green">
					<div id="myBar" class="w3-container w3-deep-purple w3-center" style="width:
						<?php echo usedDisk()?>">
						<?php echo usedDisk()?>
					</div>
				</div>
			</div>
			<form class="w3-content" id="form1" action="userDownloads.php" method="post">
				<div class="w3-container">
					<h2><i class="glyphicon glyphicon-download-alt"></i> Descarregar-se vídeos</h2>
					<div class="w3-panel w3-deep-purple">
						<h3>Info!</h3>
						<p>Potser tarden en apareixer físicament per que és descarreguen en segon plà!</p>
					</div>
					<div class="w3-row-padding">
						<div class="w3-container w3-panel">
							<label><i class="glyphicon glyphicon-link"></i> <b>Link del vídeo</b></label></br>
							<input class="form-control w3-round-large w3-margin-top" type="text" name="link" value="<?php echo $_POST['link'];?>"  placeholder="Introdueix el link del vídeo" required></br>
							<button class="w3-button w3-deep-purple w3-hover-shadow w3-hover-text-white w3-round-large" type="submit" name="botoPrevia">Vista previa</button>
						</div>
					</div>
				</div>
			</form>
			<?php
				session_start();
				$_SESSION['link']=$_POST['link'];
				?>
			<?php echo IfExistsPostLinkPreviewVideo();?>
			<div class="w3-container w3-display-container w3-margin">
				<div class="w3-card w3-display-topmiddle" style="width: 50%;">
					<a target="_blank" href="<?php echo $_POST['link']; ?>">
					<img class="w3-card w3-hover-sepia" src="
						<?php
							echo "https://img.youtube.com/vi/".get_youtube_id($_POST["link"])."/0.jpg";
							?>
						" alt="Minions" width="100%">
					</a>
					<div class="w3-container w3-center">
						<h5><?php echo get_youtube_title(get_youtube_id($_POST["link"]));?></h5>
					</div>
					<!-- Per que descarregui el video real s'ha de redirigir el action del form a videoDownload.php -->
					<form class="w3-margin-bottom"  action="videoDownload.php" method="post">
						<label><i class="glyphicon glyphicon-link"></i> <b>Nom del vídeo</b></label></br>
						<div class=" w3-center w3-margin">
							<input class="form-control w3-round-large w3-margin-top" type="text" name="nameVideo" value="<?php echo $_POST['nameVideo'];?>" placeholder="Introdueix el nom del vídeo" required></br>
							<select class="w3-select w3-margin-bottom w3-center w3-border-0" name="folderName" style="width: 100%;" required>
								<option value="" disabled selected>Escull l'ubicació.</option>
								<?php
									foreach (lsFolders() as $valor) {
									  echo '<option value="'.$valor.'">'.$valor.'</option>';
									}
									?>
							</select>
							</br>
							<button class="w3-button w3-deep-purple w3-hover-shadow w3-hover-text-white" style="width:100%; height: 100%;" type="submit" name="downloadButton" autofocus>Descarregar Vídeo</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--Script menú-->
		<script>
			function w3_open() {
			    document.getElementById("mySidebar").style.display = "block";
			}
			function w3_close() {
			    document.getElementById("mySidebar").style.display = "none";
			}
		</script>
	</body>
</html>
