<?php
  include 'funcions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú RPI Projecte</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="w3.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body class="w3-light-grey" onload=' location.href="#myanchor"'>
  <?php
  //SSH Credentials RPI
    $config = parse_ini_file('scripts/client.ini');
    $userSSH = $config['userSSH'];
    $passwordSSH = $config['passwordSSH'];

    session_start();
    $link=$_SESSION['link'];
    $nameVideo=$_POST['nameVideo'];
    $folderName=$_POST['folderName'];
    $userID=$_SESSION['userID'];

    //Guardem les dades del vídeo a la BDD
    $noSpacesNameFolder = str_replace(" ","",$folderName);
    $noSpacesNameVideo= str_replace(" ","",$nameVideo);
    downloadVideo($nameVideo,$folderName,$userID,$link);
    ignore_user_abort();
    $a = popen('sshpass -p '.$passwordSSH.' ssh -oStrictHostKeyChecking=no '.$userSSH.'@'.getIPRPI().' /home/'.$userSSH.'/scripts/downloader.sh '.$link.' /home/'.$userSSH.'/videos/'.$noSpacesNameFolder.' '.$noSpacesNameVideo.' 2>&1', 'r');
    echo '<div class="container">';
    echo '<div class="row">';
    echo'<div class="col align-self-start w3-green"><div>';
    //echo "<div class='w3-third w3-green w3-margin w3-display-middle'>";
    while($b = fgets($a, 2048)) {
      echo "<span class='w3-margin-top w3-margin-right'>$b<span>";
      ob_flush();flush();
    }
    pclose($a);
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo '
    <form action="userDownloads.php" method="post">
      <button class="w3-button w3-deep-purple w3-hover-shadow w3-hover-text-white w3-margin-top" style="width:100%; height: 100%;" type="submit" name="backButton" autofocus>Tornar Enrere</button>
    </form>';
  ?>
</body>
</html>
