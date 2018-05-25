<?php
	include "funcions.php";
	session_start();
	if(empty($_SESSION['username'])){
	    header("Location: /login.php"); /* Redirect browser */
	    exit();
	}
	elseif(empty(listRPI()[0])){
	  header("Location: /registerRPI.php");
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Home RPI Project</title>
		<link rel="icon" type="image/png" href="images/logo.png">
		<link rel="stylesheet" href="w3.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	</head>
	<body class="w3-light-grey">
		<!--header-->
		<div>
			<header class="w3-row w3-teal w3-deep-purple w3-card-2">
				<h1 class="w3-animate-left">
					<div class="w3-teal" style="float:left;">
						<button class="w3-button w3-teal w3-xlarge" onclick="w3_open()">&#9776;</button>
					</div>
					<div style="float:left; margin-left: 10px; margin-bottom: 10px;">RPI Video Projecte <i class="glyphicon glyphicon-home"></i>
					</div>
				</h1>
			</header>
		</div>
		<!-- Sidebar -->
		<div class="w3-sidebar w3-bar-block" style="display:none" id="mySidebar">
			<button onclick="w3_close()" class="w3-bar-item w3-button w3-large w3-hover-text-white">Tancar &times;</button>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="#"><i class="glyphicon glyphicon-home"></i> Home</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="videos.php"><i class="glyphicon glyphicon-film"></i> Reproducció</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="userDownloads.php"><i class="glyphicon glyphicon-download-alt"></i> Descarrega</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="schedules.php"><i class="glyphicon glyphicon-calendar"></i> Horaris</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="logout.php"><i class="glyphicon glyphicon-log-out"></i> Tancar Sessió</a>
		</div>
		<!-- Page Content -->
		<div onclick="w3_close()">
		<div class="w3-container w3-margin">
		<div class="w3-container w3-center" style="width:100%;">
			<div>
				<div class="mySlides">
					<div class="w3-container backgroundWeb">
						<h1><b>RPI Play <span class="badge badge-primary">V1.0</span></b></h1>
						<h1><i></i></h1>
					</div>
				</div>
				<div class="mySlides">
					<div class="w3-container backgroundWeb">
						<h1><b>Benvingut/da <?php echo $_SESSION['username'];?></b></h1>
						<h1><i></i></h1>
					</div>
				</div>
				<!-- Comprovem el estat de la raspberry -->
				<div class="container d-flex h-100">
					<div class="w3-container w3-margin customShadowSlim row justify-content-center align-self-center">
						<h2><i class="glyphicon glyphicon-wrench"></i> Administració RPI</h2>
						<p>Estat de la RPI:</p>
						<?php
							echo '
							 <span class="w3-animate-zoom w3-large w3-margin">
							    '.statusRPI().'
							</span>';
							?>
						<div id="divRPI" class="w3-margin" style="display:none">
							<i class="glyphicon glyphicon-chevron-up"></i>
							<p><span class="label label-default">Model =
								<?php
									//echo listRPI()[0]["nomModel"];
									switch (listRPI()[0]["nomModel"]) {
									  case "a01040":
									      echo "RPI 2 Model B";
									      break;
									  case "a01041":
									      echo "RPI 2 Model B Q1 2015";
									      break;
									  case "a21041":
									      echo "RPI 2 Model B Q1 2015 ";
									      break;
									  case "a22042":
									      echo "RPI 2 Model B (with BCM2837) Q3 2016";
									      break;
									  case "a02082":
									      echo "RPI 3 Model B Q1 2016";
									      break;
									  case "a22082":
									      echo "RPI 3 Model B Q1 2016 ";
									      break;
									  case "a32082":
									      echo "RPI 3 Model B Q4 2016";
									      break;
									  case "a020d3":
									      echo "RPI 3 Model B+ Q1 2018";
									      break;
									}?></span>
							</p>
							<p><span class="label label-default">Serial Number = <?php echo listRPI()[0]["numSerie"]; ?></span></p>
							<p><span class="label label-default">IP = <?php echo getIPRPI();?></span></p>
						</div>
						<form action="" method="POST">
							<button class="w3-button w3-purple w3-hover-shadow w3-hover-text-white w3-margin" name="updateRPI" value="updateRPI"><i class="glyphicon glyphicon-save-file"></i> Actualitzar RPI</button>
							<button class="w3-button w3-purple w3-hover-shadow w3-hover-text-white w3-margin" name="restartRPI" value="restartRPI"><i class="glyphicon glyphicon-repeat"></i> Reiniciar RPI</button>
						</form>
					</div>
					<?php
						//Comprovació dels botons d'administració execució de lés funcions
						if(isset($_POST['updateRPI'])){
							echo "Actualitzant RPI";
						}
						elseif($_POST['restartRPI']){
							restartRPI();
						}
						?>
				</div>
				<hr>
				<div class="w3-container w3-center">
					<?php
						//Si existeix la sessió play, mostrem els vídeos que s'estan reproduïnt.
						if(isset($_SESSION['play'])){
						$arrayVideos=showDBVideos($_SESSION['play']);
						$numVideos=count(showDBVideos($_SESSION['play']));
						$dynamicClass=dynamicDiv($numVideos);
						    echo '<h2><i class="glyphicon glyphicon-play w3-margin"></i>Vídeos s\'estan reproduïnt actualment <span class="w3-deep-purple label label-default"><?php echo count(showDBVideos($_SESSION["play"]));?></span></h2>';
					foreach($arrayVideos as $clau => $valor){
					$div='
					<div class="'.$dynamicClass.'">
						<div class="w3-card w3-animate-opacity w3-margin-right w3-margin-bottom">
							<a target="_blank" href="'.$valor.'">
							<img class="w3-round w3-card w3-hover-sepia" src="https://img.youtube.com/vi/'.get_youtube_id($valor).'/0.jpg" alt="'.$clau.'" width="100%">
							</a>
							<div class="w3-container w3-deep-purple">
								<h5>'.$clau.'</h5>
							</div>
						</div>
					</div>
					';
					echo $div;
					}
					}
					else{
					echo'
					<div class="w3-panel w3-deep-purple">
						<h3>Info!</h3>
						<p>No s\'esta reproduïnt cap carpeta.</p>
					</div>
					';
					}
					?>
				</div>
			</div>
		</div>
		<!-- JavaScript menú -->
		<script>
			function w3_open() {
			    document.getElementById("mySidebar").style.display = "block";
			}
			function w3_close() {
			    document.getElementById("mySidebar").style.display = "none";
			}
		</script>
		<!-- JavaScript Slide Versió/Nom d'usuari -->
		<script>
			var slideIndex = 0;
			carousel();

			function carousel() {
			    var i;
			    var x = document.getElementsByClassName("mySlides");
			    for (i = 0; i < x.length; i++) {
			      x[i].style.display = "none";
			    }
			    slideIndex++;
			    if (slideIndex > x.length) {slideIndex = 1}
			    x[slideIndex-1].style.display = "block";
			    setTimeout(carousel, 2000);
			}
		</script>
		<script>
			$('#statusButton').click(function() {
			$('#divRPI').slideToggle(function() {
			// Animation complete.
			 });
			});
		</script>
	</body>
</html>
