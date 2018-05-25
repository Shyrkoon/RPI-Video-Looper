<?php
    session_start();
    if(empty($_SESSION['username'])){
        header("Location: /login.php"); /* Redirect browser */
        exit();
    }
    include "funcions.php";
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
</head>
<body class="backgroundWeb">

<!-- Page Content -->
  <div class="w3-container w3-center w3-margin">
    <div class="mySlides">
      <div class="w3-container w3-deep-purple">
      <h1><b>RPI Play <span class="badge badge-primary">V1.0</span></b></h1>
      <h1><i></i></h1>
    </div>
    </div>
    <div class="mySlides">
      <div class="w3-container w3-deep-purple">
        <h1><b>Benvingut/da <?php echo $_SESSION['username'];?></b></h1>
        <h1><i></i></h1>
      </div>
    </div>
    <a class="w3-margin nounderline w3-bar-item w3-button w3-hover-text-white w3-purple w3-right" href="logout.php"><i class="glyphicon glyphicon-log-out"></i> Tancar Sessió</a>
    <div class="w3-container w3-margin w3-center">
      <h1>Començem, primer has de registrar la RPI.</h1>
      <h1><i class="glyphicon glyphicon-arrow-down"></i></h1>
      <div class="w3-container" style="width:25%;margin:auto;">
          <?php
          if(empty(listRPI())){
            echo '
            <form action="" method="POST">
              <input class="w3-center form-control w3-round-large" type="text" name="macRPI" placeholder="Introdueix la MAC de la RPI que vols activar." required></br>
              <button class="w3-button w3-purple w3-hover-shadow w3-hover-text-white" name="registerRPI" value="registerRPI"><i class="glyphicon glyphicon-ok-circle"></i> Registrar RPI</button>
            </form>';
          }
          ?>
      </div>
    </div>
    <?php
      if(isset($_POST['registerRPI'])){
          echo '
            <h2 class="w3-margin">'.registerRPI($_POST["macRPI"]).'</h2>';
      }
    ?>
  </div>
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
</body>
</html>
