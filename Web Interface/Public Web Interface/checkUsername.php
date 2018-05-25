<?php

if($_POST['name'])
  {
    $routedbINI="scripts/db.ini"; 
    $name = strip_tags($_POST['name']);
    $config = parse_ini_file($routedbINI);
    $host = $config['host'];
    $db = $config['db'];
    $userBDD = $config['userdb'];
    $dbPass = $config['passworddb'];

    $DBH = new PDO("mysql:host=$host; dbname=$db",$userBDD,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));
    $statement = $DBH->prepare("SELECT usuari FROM usuaris WHERE usuari=:usuari");
    $statement->execute(array(':usuari'=>$name));
    $count = $statement->rowCount(); 
    if($count>0)
      {
        echo "<span style='color: #e80000;'>El usuari: ".$name." ja està registrat.</span>";
  }
    else
       {
	  echo "<span style='color: white;'>".$name." està disponible.</span>";
    }
  }
?>