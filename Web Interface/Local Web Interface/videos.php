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
		<title>Menú RPI Project</title>
		<link rel="icon" type="image/png" href="images/logo.png">
		<link rel="stylesheet" href="w3.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	</head>
	<body class="w3-light-grey">
		<!--Header-->
		<div>
			<header class="w3-row w3-teal w3-deep-purple w3-card-2">
				<h1 class="w3-animate-left">
					<div class="w3-teal" style="float:left;">
						<button class="w3-button w3-teal w3-xlarge" onclick="w3_open()">&#9776;</button>
					</div>
					<div style="float:left; margin-left: 10px; margin-bottom: 10px;">RPI Video Projecte <i class="glyphicon glyphicon-film"></i></div>
				</h1>
			</header>
		</div>
		<!-- Sidebar -->
		<div class="w3-sidebar w3-bar-block" style="display:none; position: absolute;" id="mySidebar">
			<button onclick="w3_close()" class="w3-bar-item w3-button w3-large w3-hover-text-white">Tancar &times;</button>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="home.php"><i class="glyphicon glyphicon-home"></i> Home</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="#"><i class="glyphicon glyphicon-film"></i> Reproducció</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="userDownloads.php"><i class="glyphicon glyphicon-download-alt"></i> Descarrega</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="schedules.php"><i class="glyphicon glyphicon-calendar"></i> Horaris</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="logout.php"><i class="glyphicon glyphicon-log-out"></i> Tancar Sessió</a>
		</div>
		<!-- Page Content -->
		<div onclick="w3_close()">
			<div class="w3-container w3-margin w3-center">
				<?php
					if(isset($_SESSION['play'])){
					  echo'
					  <div class="w3-panel w3-deep-purple">
					      <h3>S\'esta reproduïnt la carpeta: '.$_SESSION['play'].'</h3>
					      <p>Per parar la reproducció s\'ha de seleccionar la carpeta i clicar al botó <i class="glyphicon glyphicon-stop"></i></p>
					  </div>';
					}
					?>
				<form class="w3-content" action="videos.php" method="POST">
					<h3><i class="glyphicon glyphicon-folder-open"></i> Carpetes <span class="w3-deep-purple label label-default"><?php echo count(lsFolders())?></span></h3>
					<input class="form-control w3-center" type="text" name="newFolder" placeholder="Escull el nom de la carpeta que vols crear." required></br>
					<button class="w3-button w3-deep-purple w3-hover-shadow w3-hover-text-white" type="submit" name="newFolderButton">Crear Carpeta</button></br>
				</form>
				<?php
					if(isset($_POST['newFolderButton'])){
					  if(!empty($_POST['newFolder'])){
					    newFolder($_POST['newFolder']);
					    header("Location:videos.php");
					  }
					}
					?>
				<button class="w3-button w3-teal w3-margin" onclick="myFunction('delFolder')"><i id="collapse-down" class="glyphicon glyphicon-collapse-down"></i></button>
				<div id="delFolder" class="w3-hide">
					<form action="" method="POST">
						<h3><i class="glyphicon glyphicon-chevron-right"></i> Selecciona la carpeta que vols eliminar</h3>
						<?php
							//Listbox de les carpetes que volem modificar
							$numFolders=count(lsFolders());
							echo '
							<select name="lsSelectDel" size="'.$numFolders.'">';
								foreach (lsFolders() as $valor) {
							                             	echo '<option value="'.$valor.'">'.$valor.'&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</option>';
							                     	}
							echo '</select></br>';
							?>
						<button class="w3-button w3-deep-purple w3-hover-shadow w3-hover-text-white w3-margin" type="submit" name="buttonDelFolder"> <i class="glyphicon glyphicon-trash"></i></button>
					</form>
					<?php
						if(isset($_POST['buttonDelFolder'])){
						    delFolder($_POST['lsSelectDel']);
						    header("Refresh:0");
						}
						?>
				</div>
				<hr>
				<form class="w3-content" action="" method="POST">
					<h3><i class="glyphicon glyphicon-folder-open"></i> Escull la carpeta dels videos que vols reproduïr o parar de reproduïr</h3>
					<select class="form-control" name="nameFolder" onchange="this.form.submit()">
						<option value="" disabled selected>Escull l'ubicació.</option>
						<?php
							//Per cada carpeta creem una etiqueta option
							foreach (lsFolders($_POST['nameFolder'],$_SESSION['userID']) as $clau => $valor) {
									echo '<option value="'.$valor.'">'.$valor.'</option>';
							}
							?>
					</select>
				</form>
				<!--Miniatura vídeos-->
				<div class="w3-row-padding">
					<h3><i class="glyphicon glyphicon-film"></i> Videos <span class="w3-deep-purple label label-default"><?php echo count(showDBVideos($_POST["nameFolder"]))?></span></h3>
					<?php
						//Mostrar el botó de reproducció només si s'ha indicat l'ubicació i existeixen vídeos
						if(empty(showDBVideos($_POST['nameFolder']))){
						    echo'
						    <hr>
						    <div class="w3-panel w3-margin">
						        <p>No hi ha cap vídeo o no s\'ha seleccionat cap carpeta.</p>
						    </div>';
						}
						else{
						    if(isset($_POST['nameFolder'])){
						        $location=$_POST['nameFolder'];
						        $_SESSION['selectedFolder']=$_POST['nameFolder'];
						        echo '<h3>Carpeta: '.$location.'</i></h3>';
						        echo '<div class="w3-deep-purple w3-margin w3-animate-opacity">';
						        echo '<form action="" method="POST">';
						            echo '</br><button class="btn w3-center w3-xxxlarge w3-circle w3-red w3-margin w3-animate-left" title="Play" type="submit" name="play" value="'.$location.'"><i class="glyphicon glyphicon-play"></i></button>';
						            echo '<button class="btn w3-center w3-xxxlarge w3-circle w3-red w3-margin w3-animate-right" title="Stop" type="submit" name="stop" value="'.$location.'"><i class="glyphicon glyphicon-stop"></i></button></br>';
						        echo '</form>';
								echo '</div>';
							}
						}
						?>
					<?php
						//Reproduïr o parar la carpeta indicada
						if(isset($_POST['play'])){
						  playFolder($_POST['play']);
						  echo "<meta http-equiv='refresh' content='0'>";
						}
						elseif (isset($_POST['stop'])) {
						  stopFolder($_POST['stop']);
						  echo "<meta http-equiv='refresh' content='0'>";
						}
						?>
					<?php
						//Miniatura dels vídeos de la carpeta seleccionada.
						$arrayVideos=showDBVideos($_POST['nameFolder']);
						$numVideos=count($arrayVideos);
						$dynamicClass=dynamicDiv($numVideos);
						foreach($arrayVideos as $name => $link){
						  $div='<div id="'.$link.'" class="'.$dynamicClass.' w3-animate-opacity w3-animate-zoom zoom w3-margin-top">
						    <div class="w3-card w3-display-container">
						       <form action="" method="POST">
						           <button id="'.$name.'" class="w3-button w3-red w3-large w3-display-topright" name="buttonTrash" value="'.$name.'"><i class="glyphicon glyphicon-trash"></i></button>
						       </form>
						        <a target="_blank" href="'.$link.'">
						            <img class="w3-card" src="https://img.youtube.com/vi/'.get_youtube_id($link).'/0.jpg" alt="'.$name.'" width="100%">
						        </a>
						        <div class="w3-container w3-center w3-deep-purple">
						            <h5>'.$name.'</h5>
						        </div>
						      </div>
						  </div>';
						    echo $div;
						    $i++;
						}
						?>
					<?php
						if(isset($_POST['buttonTrash'])){
						   //Parem la reproducció de la carpeta si eliminem un vídeo
						   session_start();
						   stopFolder($_SESSION['play']);

						   //Borrem el vídeo
							echo'
						   <div class="w3-panel w3-green w3-display-container">
						 	  <h3><i class="glyphicon glyphicon-trash"></i> Vídeo Eliminat</h3>
						     <p>S\'ha borrat el vídeo '.$_POST['buttonTrash'].'</p>
							</div>';
						   echo delVideo($_SESSION['selectedFolder'], $_POST['buttonTrash']);
						   echo "<meta http-equiv='refresh' content='0'>";
						}
						?>
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
		<!-- Script per tancar el menú al clicar a fora del menu -->
		<script>
			// Get the modal
			var modal = document.getElementById('id01');

			// When the user clicks anywhere outside of the modal, close it
			window.onclick = function(event) {
			    if (event.target == modal) {
			        modal.style.display = "none";
			    }
			}
		</script>
		<!-- Script per amagar el menú de borrar carpeta -->
		<script>
			function myFunction(id) {
			    var x = document.getElementById(id);
			    var element = document.getElementById("collapse-down");
			    if (x.className.indexOf("w3-show") == -1) {
			        x.className += " w3-show";
			    element.className= element.className.replace("glyphicon glyphicon-collapse-down", "glyphicon glyphicon-collapse-up");
			    } else {
			        x.className = x.className.replace(" w3-show", "");
			    element.className= element.className.replace("glyphicon glyphicon-collapse-up", "glyphicon glyphicon-collapse-down");
			    }
			}
		</script>
	</body>
</html>
