<?php
/**
 * Created by PhpStorm.
 * User: José
 * Date: 20/03/2018
 * Time: 16:57
 */

#SSH Credentials RPI

$config = parse_ini_file('scripts/client.ini');
$userSSH = $config['userSSH'];
$passwordSSH = $config['passwordSSH'];

#Change with your db.ini location.
$routedbINI="scripts/db.ini";

#Funció per saber la IP de la RPI registrada.
function getIPRPI(){
  try{
        global $routedbINI;
        $config = parse_ini_file($routedbINI);
        $host = $config['host'];
        $db = $config['db'];
        $userBDD = $config['userdb'];
        $dbPass = $config['passworddb'];

        $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

        $sql = $DBH -> prepare("SELECT ipPublica FROM raspberries WHERE idPropietari = :idPropietari");

        $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql -> bindParam(':idPropietari', $_SESSION['userID']);

        $sql -> execute();

        $output = $sql->fetch(PDO::FETCH_COLUMN);

        return $output;
    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
}

#Funció per extreure la id d'un vídeo d'una URL Youtube
function get_youtube_id($url){
    parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
    return $my_array_of_vars['v'];
}
// Output: C4kxS1ksqtw

#Funció per extreure el títol de una URL de Youtube
function get_youtube_title($id){
    $json = file_get_contents('http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=' . $id . '&format=json'); //get JSON video details
    $details = json_decode($json, true); //parse the JSON into an array
    return $details['title']; //return the video title
}
// Output: Títol del vídeo

#Funció per calcular l'espai lliure del sistema operatiu, en el nostre cas la RPI
function usedDisk(){
    global $userSSH;
    global $passwordSSH;

    $disk_used_p=shell_exec("sshpass -p '$passwordSSH' ssh -oStrictHostKeyChecking=no $userSSH@".getIPRPI()." df --output=pcent / | sed 1d");
    return $disk_used_p;
}

#Funció per amagar el contenidor de preview de vídeos quan no te ninguna URL
function IfExistsPostLinkPreviewVideo(){
    if(isset($_POST['link'])){
        return '<div class="w3-container">';
    }
    else{
        return '<div class="w3-container w3-hide">';
    }
}

#Funció per amagar el error de login quan no succeeix
function hideLoginError(){
    #Aquesta sessió és crea a dbConnection.php
    if($_SESSION['errorLogin']){
	unset($_SESSION["errorLogin"]);
        return "w3-show";
    }
    else{
        return "w3-hide";
    }
}

#Funció per adaptar els divs dels videos depenent de la quantitat de videos
function dynamicDiv($numVideos){
  if($numVideos==1){
    $class="w3-content";
    return $class;
  }
  elseif ($numVideos==2) {
    $class="w3-half";
    return $class;
  }
  elseif($numVideos==3){
    $class="w3-third";
    return $class;
  }
  else{
    $class="w3-quarter";
    return $class;
  }
}

#Funció per crear una carpeta a la RPI
function newFolder($nameFolder){
    global $routedbINI;
    global $userSSH;
    global $passwordSSH;

    $noSpacesNameFolder = str_replace(" ","",$nameFolder);
	$config = parse_ini_file($routedbINI);
    $host = $config['host'];
    $db = $config['db'];
    $userBDD = $config['userdb'];
    $dbPass = $config['passworddb'];

    $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

    $statement = $DBH->prepare("INSERT INTO carpetes(nomCarpeta,idPropietari) VALUES(:nomCarpeta,:idPropietari)");
    $data=array('nomCarpeta' => $nameFolder, 'idPropietari' => $_SESSION['userID']);
    $statement->execute($data);

    shell_exec("sshpass -p $passwordSSH ssh -oStrictHostKeyChecking=no $userSSH@".getIPRPI()." mkdir /home/$userSSH/videos/$noSpacesNameFolder");

  	$output='<div class="w3-panel w3-green w3-display-container">
  		<span onclick="this.parentElement.style.display=\'none\'" class="w3-button w3-green w3-large w3-display-topright">&times;</span>
  	<h3>Carpeta creada!</h3>
  	<p><i class="glyphicon glyphicon-ok-circle"></i> S\'ha creat la carpeta '.$nameFolder.'</p>
  	</div>';
  	return $output;
}

#Funció per eliminar una carpeta de la RPI
function delFolder($nameFolder){
    global $routedbINI;
    global $userSSH;
    global $passwordSSH;
    $noSpacesNameFolder = str_replace(" ","",$nameFolder);
    #Borrem la carpeta
    $config = parse_ini_file($routedbINI);
    $host = $config['host'];
    $db = $config['db'];
    $userBDD = $config['userdb'];
    $dbPass = $config['passworddb'];

    $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

    $statement = $DBH->prepare("DELETE FROM carpetes WHERE nomCarpeta = :nomCarpeta AND idPropietari= :idPropietari");
    $data=array('nomCarpeta' => $nameFolder, 'idPropietari' => $_SESSION['userID']);
    $statement->execute($data);

    #Borrem els videos de la BDD
    $config = parse_ini_file($routedbINI);
    $host = $config['host'];
    $db = $config['db'];
    $userBDD = $config['userdb'];
    $dbPass = $config['passworddb'];

    $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

    $statement = $DBH->prepare("DELETE FROM multimedia WHERE localitzacio = :localitzacio AND idPropietari= :idPropietari");
    $data=array('localitzacio' => $nameFolder, 'idPropietari' => $_SESSION['userID']);
    $statement->execute($data);

    #Borrem la carpeta en la RPI
    shell_exec('sshpass -p "'.$passwordSSH.'" ssh -oStrictHostKeyChecking=no '.$userSSH.'@'.getIPRPI().' rm -r "/home/'.$userSSH.'/videos/'.$noSpacesNameFolder.'"');
}

#Funció per llistar les carpetes disponibles a las RPI
function lsFolders(){
    try{
        global $routedbINI;
        $config = parse_ini_file($routedbINI);
        $host = $config['host'];
        $db = $config['db'];
        $userBDD = $config['userdb'];
        $dbPass = $config['passworddb'];

        $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

        $sql = $DBH -> prepare("SELECT idCarpeta,nomCarpeta FROM carpetes WHERE idPropietari = :idPropietari");

        $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql -> bindParam(':idPropietari', $_SESSION['userID']);

        $sql -> execute();

        $output = $sql->fetchAll(PDO::FETCH_KEY_PAIR);

        return $output;

    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
}

#Funció per guardar a la BDD les dades dels vídeos que posi el usuari a userDownloads.php i descarregar-los amb el script youtube-dl
function downloadVideo($nameVideo,$nameFolder,$userID,$link){
    try {
        global $routedbINI;
        $config = parse_ini_file($routedbINI);
        $host = $config['host'];
        $db = $config['db'];
        $userBDD = $config['userdb'];
        $dbPass = $config['passworddb'];

        $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

        $statement = $DBH->prepare("INSERT INTO multimedia(nomVideo,localitzacio,idPropietari,link) VALUES(:nomVideo,:localitzacio,:idPropietari,:link)");
        $data=array('nomVideo' => $nameVideo, 'localitzacio' => $nameFolder, 'idPropietari' => $userID, 'link' => $link);
        $statement->execute($data);

    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

#Funció per retornar una array amb els vídeos que estiguin registrats a la BDD, necesita una ubicació
function showDBVideos($nameFolder){
    try{
        global $routedbINI;
        $config = parse_ini_file($routedbINI);
        $host = $config['host'];
        $db = $config['db'];
        $userBDD = $config['userdb'];
        $dbPass = $config['passworddb'];

        $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

        $sql = $DBH -> prepare("SELECT nomVideo,link FROM multimedia WHERE localitzacio = :localitzacio AND idPropietari = :idPropietari");

        $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql -> bindParam(':localitzacio', $nameFolder);

        $sql -> bindParam(':idPropietari', $_SESSION['userID']);

        $sql -> execute();

        $output = $sql->fetchAll(PDO::FETCH_KEY_PAIR);

        return $output;
    }
    catch(PDOException $e){
            echo $e->getMessage();
    }
}

#Funció per eliminar un vídeo
function delVideo($nameFolder, $nameVideo){
    try{
    global $routedbINI;
    global $userSSH;
    global $passwordSSH;
    $noSpacesNameFolder = str_replace(" ","",$nameFolder);
    $noSpacesNameVideo = str_replace(" ","",$nameVideo);
    $config = parse_ini_file($routedbINI);
    $host = $config['host'];
    $db = $config['db'];
    $userBDD = $config['userdb'];
    $dbPass = $config['passworddb'];

    $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

    $statement = $DBH->prepare("DELETE FROM multimedia WHERE nomVideo = :nameVideo AND idPropietari= :idPropietari");
    $data=array('nameVideo' => $nameVideo, 'idPropietari' => $_SESSION['userID']);
    $statement->execute($data);
    //Ruta del fitxer que volem borrar.
    $route="/home/$userSSH/videos/$noSpacesNameFolder/$noSpacesNameVideo.mp4";
    shell_exec("sshpass -p '$passwordSSH' ssh -oStrictHostKeyChecking=no $userSSH@".getIPRPI()." rm -r $route");
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
}

#Funció per reproduïr els vídeos de la carpeta seleccionada a videos.php
function playFolder($nameFolder){
    global $userSSH;
    global $passwordSSH;
	session_start();
    $_SESSION['play']=$nameFolder;
    $noSpacesNameFolder = str_replace(" ","",$nameFolder);
    shell_exec("sshpass -p '$passwordSSH' ssh -oStrictHostKeyChecking=no $userSSH@".getIPRPI()." sudo /home/project/scripts/startvideo.sh stop");
    shell_exec("sshpass -p '$passwordSSH' ssh -oStrictHostKeyChecking=no $userSSH@".getIPRPI()." sudo nohup /home/$userSSH/scripts/startvideo.sh start /home/$userSSH/videos/$noSpacesNameFolder/ >/dev/null 2>/dev/null &");
}
#Funció per parar la reproducció dels videos de una carpeta
function stopFolder($nameFolder){
    global $userSSH;
    global $passwordSSH;
	session_start();
    unset($_SESSION['play']);
    shell_exec("sshpass -p '$passwordSSH' ssh -oStrictHostKeyChecking=no $userSSH@".getIPRPI()." sudo /home/$userSSH/scripts/startvideo.sh stop");
}

#Funció per comprovar estat de la RPI
function statusRPI(){
    global $userSSH;
    global $passwordSSH;
	$output=shell_exec("scripts/./checkStatus.sh $userSSH $passwordSSH ".getIPRPI()." 2>&1");
	return $output;
}

#Funció per crear un Horari, tant a la BDD com a la RPI
function createSchedule($action,$nameSchedule,$time,$nameFolder){
    try {
        global $routedbINI;
        global $userSSH;
        global $passwordSSH;

        $noSpacesNameFolder = str_replace(" ","",$nameFolder);
        $noSpacesNameSchedule = str_replace(" ","",$nameSchedule);
        //Creem l'horari a la bdd
        $config = parse_ini_file($routedbINI);
        $host = $config['host'];
        $db = $config['db'];
        $userBDD = $config['userdb'];
        $dbPass = $config['passworddb'];

        $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

        $statement = $DBH->prepare("INSERT INTO horaris(nomHorari,hora,nomCarpetaHorari,idPropietariHorari) VALUES(:nomHorari,:hora,:nomCarpetaHorari,:idPropietariHorari)");
        $data=array('nomHorari' => $nameSchedule, 'hora' => $time, 'nomCarpetaHorari' => $nameFolder, 'idPropietariHorari' => $_SESSION['userID']);
        $statement->execute($data);

        //Enviem al script d'horaris de la RPI les dades del nou horari

        $splitHourMinute = explode(":", $time);
        shell_exec('sshpass -p '.$passwordSSH.' ssh -oStrictHostKeyChecking=no '.$userSSH.'@'.getIPRPI().' /home/'.$userSSH.'/scripts/./scheduler.sh create "'.$noSpacesNameSchedule.' '.$splitHourMinute[1].' '.$splitHourMinute[0].' '.$noSpacesNameFolder.'"');
    }

    catch (Exception $e) {
        echo $e->getMessage();
    }
}

#Funció per eliminar un Horari, tant a la BDD com a la RPI
function deleteSchedule($nameSchedule){
    try{
        global $routedbINI;
        global $userSSH;
        global $passwordSSH;

        $noSpacesNameSchedule = str_replace(" ","",$nameSchedule);
        //Eliminem l'horari
        $config = parse_ini_file($routedbINI);
        $host = $config['host'];
        $db = $config['db'];
        $userBDD = $config['userdb'];
        $dbPass = $config['passworddb'];

        $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

        $statement = $DBH->prepare("DELETE FROM horaris WHERE nomHorari = :nomHorari AND idPropietariHorari = :idPropietariHorari");
        $data=array('nomHorari' => $nameSchedule, 'idPropietariHorari' => $_SESSION['userID']);
        $statement->execute($data);

        shell_exec("sshpass -p $passwordSSH ssh -oStrictHostKeyChecking=no $userSSH@".getIPRPI()." /home/".$userSSH."/scripts/./scheduler.sh delete $noSpacesNameSchedule");

    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
}

#Funció per consultar els horaris del usuari que ha iniciat sessió
function getSchedule(){
    try{
        global $routedbINI;
        $config = parse_ini_file($routedbINI);
        $host = $config['host'];
        $db = $config['db'];
        $userBDD = $config['userdb'];
        $dbPass = $config['passworddb'];

        $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

        $sql = $DBH -> prepare("SELECT idHorari,nomHorari,hora,nomCarpetaHorari FROM horaris WHERE idPropietariHorari = :idPropietariHorari");

        $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql -> bindParam(':idPropietariHorari', $_SESSION['userID']);

        $sql -> execute();

        $output = $sql->fetchAll();

        return $output;
    }
    catch(PDOException $e){
            echo $e->getMessage();
    }
}

#Funció per saber si tens RPI registrades
function listRPI(){
        global $routedbINI;
        $config = parse_ini_file('scripts/db.ini');
        $host = $config['host'];
        $db = $config['db'];
        $userBDD = $config['userdb'];
        $dbPass = $config['passworddb'];

  $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

  $sql = $DBH -> prepare("SELECT ipPublica,numSerie,nomModel FROM raspberries WHERE idPropietari = :idPropietari");

  $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $sql -> bindParam(':idPropietari', $_SESSION['userID']);

  $sql -> execute();

  $output = $sql->fetchAll();

  return $output;
}

function registerRPI($mac){
  //Eliminem l'horari
    global $routedbINI;
    $config = parse_ini_file($routedbINI);
    $host = $config['host'];
    $db = $config['db'];
    $userBDD = $config['userdb'];
    $dbPass = $config['passworddb'];

    $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));

    $sql = $DBH -> prepare("SELECT idPropietari FROM raspberries WHERE mac = :mac");

    $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql -> bindParam(':mac', $mac);

    $sql -> execute();

    $output=$sql->fetchColumn();//fetch(PDO::FETCH_ASSOC);

    //return $output;

    if($output===NULL){
      $statement = $DBH->prepare("UPDATE raspberries SET idPropietari  = :idPropietari WHERE mac = :mac");
      $data=array('idPropietari' => $_SESSION['userID'], 'mac' => $mac);
      $statement->execute($data);
      return header("Location: /login.php");
    }
    else{
      $message="Ja registrat";
      return $message;
    }
}

function restartRPI(){
    global $userSSH;
    global $passwordSSH;
    if(isset($_SESSION['play'])){
        unset($_SESSION['play']);
    }
    shell_exec('sshpass -p '.$passwordSSH.' ssh -oStrictHostKeyChecking=no '.$userSSH.'@'.getIPRPI().' sudo init 6');
}

/*
function updateRPI(){
        global $userSSH;
        global $passwordSSH;
}
*/