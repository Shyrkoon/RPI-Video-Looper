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
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<style type="text/css">
			option{color: black;}
		</style>
	</head>
	<body class="w3-light-grey">
		<!--header-->
		<div>
			<header class="w3-row w3-teal w3-deep-purple w3-card-2">
				<h1 class="w3-animate-left">
					<div class="w3-teal" style="float:left;">
						<button class="w3-button w3-teal w3-xlarge" onclick="w3_open()">&#9776;</button>
					</div>
					<div style="float:left; margin-left: 10px; margin-bottom: 10px;">RPI Video Projecte <i class="glyphicon glyphicon-calendar"></i></div>
				</h1>
			</header>
		</div>
		<!-- Sidebar -->
		<div class="w3-sidebar w3-bar-block" style="display:none" id="mySidebar">
			<button onclick="w3_close()" class="w3-bar-item w3-button w3-large w3-hover-text-white">Tancar &times;</button>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="home.php"><i class="glyphicon glyphicon-home"></i> Home</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="videos.php"><i class="glyphicon glyphicon-film"></i> Reproducció</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="userDownloads.php"><i class="glyphicon glyphicon-download-alt"></i> Descarrega</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="#"><i class="glyphicon glyphicon-calendar"></i> Horaris</a>
			<a class="nounderline w3-bar-item w3-button w3-hover-text-white" href="logout.php"><i class="glyphicon glyphicon-log-out"></i> Tancar Sessió</a>
		</div>
		<!-- Page Content -->
		<div onclick="w3_close()">
			<div class="w3-container w3-center">
				<!-- Calendari -->
				<div class="w3-container w3-half w3-center w3-deep-purple w3-margin-top">
					<h2><i class="glyphicon glyphicon-calendar"></i> Programació d'horaris</h2>
					<form method="post" action="schedules.php">
						<!-- Date input -->
						<input class="form-control w3-round-large w3-margin-top" type="text" name="nameSchedule" placeholder="Introdueix un nom a l'horari" required></br>
						<label><i class="glyphicon glyphicon-time"></i> Hora</label></br>
						<input class="w3-text-black" type="time" name="time"></br>
						<select class="w3-select w3-margin-top w3-text-black" name="nameFolder">
							<option value="" disabled selected>Escull l'ubicació.</option>
							<?php
								//Per cada carpeta creem una etiqueta option
								foreach (lsFolders($_POST['nameFolder'],$_SESSION['userID']) as $clau => $valor) {
								    echo '<option value="'.$valor.'">'.$valor.'</option>';
								}
								?>
						</select>
						<button class="w3-button w3-purple w3-hover-shadow w3-hover-text-white w3-round-large w3-margin" name="submit" type="submit">Programar Horari</button>
					</form>
				</div>
				<div class="w3-container w3-half">
					<h2><i class="glyphicon glyphicon-ok"></i> Horaris programats actualment</h2>
					<hr>
					<div class="w3-panel w3-deep-purple w3-leftbar w3-border-purple w3-margin-top">
						<table class="w3-table w3-hoverable">
							<tr class="w3-deep-purple">
								<th style="width:10%"></th>
								<th>Nom</th>
								<th>Hora</th>
								<th>Carpeta</th>
							</tr>
							<tr class="w3-hover-purple">
								<?php
									foreach (getSchedule() as $value) {
									  // code...
									  echo "<tr>";
									  echo '<td>
									  <form action="" method="POST">
									      <button id="'.$value[1].'" class="w3-button w3-red" name="buttonDeleteSchedule" value="'.$value[1].'"><i class="glyphicon glyphicon-trash"></i></button>
									  </form>
									  </td>';
									  echo '<td>'.$value[1].'</td>';
									  echo "<td>".$value[2]."</td>";
									  echo "<td>".$value[3]."</td>";
									  echo "</tr>";
									}
									if(isset($_POST['submit'])){
									  createSchedule($_POST['actionSchedule'],$_POST['nameSchedule'],$_POST['time'],$_POST['nameFolder']);
									  echo "<meta http-equiv='refresh' content='0'>";
									}

									if(isset($_POST['buttonDeleteSchedule'])){
									  deleteSchedule($_POST['buttonDeleteSchedule']);
									  echo "<meta http-equiv='refresh' content='0'>";
									}
									?>
							</tr>
						</table>
					</div>
				</div>
				<!-- Calendari-->
				<hr>
				<!-- Video Thumbnails -->
				<div class="w3-container" style="width:100%;">
					<h2><i class="glyphicon glyphicon-play"></i> Vídeos que s'estan reproduïnt actualment.</h2>
					<?php
						if(isset($_SESSION['play'])){
						  $arrayVideos=showDBVideos($_SESSION['play']);
						  $numVideos=count(showDBVideos($_SESSION['play']));
						  $dynamicClass=dynamicDiv($numVideos);
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
						            </div>';
						    echo $div;
						  }
						}
						else{
						    echo "<hr>";
						    echo'
						    <div class="w3-panel w3-deep-purple w3-margin">
						        <h3>Info!</h3>
						        <p>No s\'esta reproduïnt cap carpeta.</p>
						    </div>';
						}
						?>
				</div>
				<!-- Video Thumbnails -->
			</div>
		</div>
		<!-- Script menú -->
		<script>
			function w3_open() {
			    document.getElementById("mySidebar").style.display = "block";
			}
			function w3_close() {
			    document.getElementById("mySidebar").style.display = "none";
			}
		</script>
		<!-- Script menú -->
		<!-- Script TimePicker -->
		<script></script>
		<!-- Script Checkbox -->
	</body>
</html>
