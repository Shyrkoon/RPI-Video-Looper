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

    register($_POST['name'], $_POST['surnames'], $_POST['email'], $_POST['user'], $_POST['password']); /*Registrem l'usuari*/
    header("Location: login.php");
}

/*Si rebem la senyal de registre fem executem la funció login*/
if(isset($_POST['submitbuttonL'])){
  //Google Captcha Check
  session_start();
    $_SESSION['user']=$_POST['user'];
    login($_POST['user'], $_POST['pass']); /*Fem el login*/
}
