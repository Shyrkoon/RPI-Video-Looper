<?php
/**
 * Created by PhpStorm.
 * User: José
 * Date: 06/03/2018
 * Time: 18:48
 */
header("Content-Type: text/html;charset=utf-8");

include('funcions.php');

$routedbINI="scripts/db.ini";

/*Funció per registrar-se a la BDD*/

function redirect($url, $statusCode = 303)
{
    header('Location: ' . $url, true, $statusCode);
    die();
}

function register($name, $surnames, $email, $username, $password) {
    global $routedbINI;
    $config = parse_ini_file($routedbINI);
    $host = $config['host'];
    $db = $config['db'];
    $userBDD = $config['userdb'];
    $dbPass = $config['passworddb'];

    $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $statement = $DBH->prepare("INSERT INTO usuaris(nom,cognoms,correu,usuari,password) VALUES(:nom,:cognoms,:correu,:usuari,:password)");
    $dades=array('nom' => $name, 'cognoms' => $surnames, 'correu'=> $email , 'usuari' => $username, 'password' => $hash);
    $statement->execute($dades);
}

/*Funció per el login d'usuaris*/
function login($username, $password) {
    global $routedbINI;
    session_start();
    $config = parse_ini_file($routedbINI);
    $host = $config['host'];
    $db = $config['db'];
    $userBDD = $config['userdb'];
    $dbPass = $config['passworddb'];

    $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

    $sql = $DBH -> prepare("SELECT password FROM usuaris WHERE usuari = :usuari");

    $sql2 = $DBH -> prepare("SELECT idUsuari FROM usuaris WHERE usuari = :usuari");

    $sql -> bindParam(':usuari',$username);

    $sql2 -> bindParam(':usuari',$username);

    $sql -> execute();

    $sql2 -> execute();

    $sql->setFetchMode(PDO::FETCH_ASSOC);

    $sql2->setFetchMode(PDO::FETCH_ASSOC);

    $hash="";

    while($row = $sql->fetch()) {
        $hash = $row['password'];
    }

    $_SESSION['userID']=0;

    while($row = $sql2->fetch()) {
        session_start();
        $_SESSION['userID'] = $row['idUsuari'];
    }

    if (password_verify($password, $hash)) {
        //login
		echo "login</br>";
		session_start();
		$_SESSION['username']=$username;
        header("Location: home.php"); /* Redirect browser */
        exit();
    } else {
        // failure
        session_start();
		$_SESSION['errorLogin']=true;
		echo $_SESSION['errorLogin'];
        header("Location: login.php"); /* Redirect browser */
        exit();
    }
}
/*Si rebem un submit del submitbuttonR fa el primer IF i registra el usuari, fa el segon if si es submitbuttonL*/
if(isset($_POST['submitbuttonR'])){
  session_start();
  $_SESSION['name']=$_POST['name'];
  $_SESSION['surnames']=$_POST['surnames'];
  $_SESSION['email']=$_POST['email'];
  $_SESSION['user']=$_POST['user'];
  //Google g-recaptcha
  $sender_name = stripslashes($_POST["sender_name"]);
	$sender_email = stripslashes($_POST["sender_email"]);
	$sender_message = stripslashes($_POST["sender_message"]);
	$response = $_POST["g-recaptcha-response"];
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array(
		'secret' => '6LcYNU4UAAAAAAWzuIMkikkNlMhxGSlpSF_qi027',
		'response' => $_POST["g-recaptcha-response"]
	);
	$options = array(
		'http' => array (
			'method' => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$verify = file_get_contents($url, false, $context);
	$captcha_success=json_decode($verify);
	if ($captcha_success->success==false) {
		header("Location: register.php"); /* Redirect browser */
	}
  else if ($captcha_success->success==true) {
      try{
          register($_POST['name'], $_POST['surnames'], $_POST['email'], $_POST['user'], $_POST['password']); /*Registrem l'usuari*/
          header("Location: login.php");
      }
      catch(PDOException $e){
          echo $e->getMessage();
      }
	}
}

/*Si rebem la senyal de registre fem executem la funció login*/
if(isset($_POST['submitbuttonL'])){
  //Google Captcha Check
  session_start();
  $_SESSION['user']=$_POST['user'];
  $sender_name = stripslashes($_POST["sender_name"]);
	$sender_email = stripslashes($_POST["sender_email"]);
	$sender_message = stripslashes($_POST["sender_message"]);
	$response = $_POST["g-recaptcha-response"];
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array(
		'secret' => '6Lfhs1UUAAAAAKds_WiP2Ydtl__YbQeRh1TlIF2V',
		'response' => $_POST["g-recaptcha-response"]
	);
	$options = array(
		'http' => array (
			'method' => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$verify = file_get_contents($url, false, $context);
	$captcha_success=json_decode($verify);
	if ($captcha_success->success==false) {
		header("Location: login.php"); /* Redirect browser */
	}
  else if ($captcha_success->success==true) {
    try{
        login($_POST['user'], $_POST['pass']); /*Fem el login*/
    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
  }
}

/*DEPRECATED
if(isset($_POST['downloadButton'])){
    try{
	session_start();
        echo '<div class="w3-container w3-green"><h3><i class="fas fa-check"></i></h3><p>S\'ha descarregat el vídeo correctament.</p></div>';

        echo '<h5>Clica a <a href="userDownloads.php"><button class="w3-button w3-deep-purple w3-hover-shadow w3-hover-text-white w3-margin">Aqui</button></a> si no redirigeix la página</h5>';

    //Rebem les dades de userDownloads.php
	downloadVideo($_POST['nameVideo'],$_POST['folderName'],$_SESSION['userID'],$_SESSION['link']);

	echo' <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">

	<div class="w3-container w3-display-middle">
  		<p><i class="fa fa-spinner w3-spin" style="font-size:64px"></i></p>
	</div>';

	header( "refresh:3;url=userDownloads.php" );
    }
    catch(PDOException $e){
            echo $e->getMessage();
        }
}
*/
