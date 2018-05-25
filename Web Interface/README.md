# This is the web interface that manages the client.

Special Thanks to:

http://phplogincode.blogspot.com.es/

https://stackoverflow.com/questions/20764031/php-salt-and-hash-sha256-for-login-password

https://www.youtube.com/watch?v=xEjv15xOl4M

http://dustwell.com/how-to-handle-passwords-bcrypt.html

https://stackoverflow.com/questions/4795385/how-do-you-use-bcrypt-for-hashing-passwords-in-php

https://www.techrepublic.com/article/how-to-create-a-self-signed-certificate-to-be-used-for-apache2/

http://www.orvis360.com/

https://www.raspberrypi.org/

https://aws.amazon.com/es/

### Webpage: http://www.rpiprojecte.cf/

* [Server Requirements](#serverrequirements)
* [Preparation and Installation](#preparation-and-installation)
* [SQL Schema](#schemasql)
* PHP Front-End:
    * [login.php](#loginphp)
    * [register.php](#registerphp)
    * [legal.html](#legalhtml)
    * [home.php](#homephp)
    * [videos.php](#videosphp)
    * [userDownload.php](#userdownloadphp)
    * [schedules.php](#schedulesphp)
    * [registerRPI.php](#registerrpiphp)
    * [videoDownload.php](#videodownloadphp)
    * [logout.php](#logoutphp)
* PHP Back-End:
    * [dbConnection.php](#dbconnectionphp)
    * [funcions.php](#funcionsphp)


## Server Requirements

* OS: Unix
* Apache2/Nginx
* PHP
* MySQL Client
* SSH
* sshpass
* Patience :smile:

## Preparation and Installation
- First you have to pass the Web Interface folder of this repository to the root of your web server, which is usually in / var / www / html and put a root document login.php.
- Edit on **login.php, register.php, dbConnection.php**, change the id of the google reCAPTCHA with your ID and allow your domain in reCAPTCHA web page.
**In case it is a local web server you will have to delete the Google reCAPTCHA part of the code or use the local code.**
- Then you have to edit the document **funcions.php, dbConnection.php** with any text editor and edit the following parameter at the beginning of the document.

```
Database INI Location
(Normally is on scripts/db.ini so by default it does not have to be modified, only if you do some security changes 
or if you want to change location.)

$routedbINI="scripts/db.ini";
```

* Edit file **client.ini** with credentials for client ssh connection.

```
; Example.

(If you normally connect with your client using another ssh option like public key, you need to change every ssh 
connection coded line on funcions.php and videoDownload.php).

[config]
; Your client username (By default is project).
userSSH = project

; Your client password (By default is project2018, so you need to change it for more security and edit this).
passwordSSH = project2018
```

* Edit the file **db.ini** with the data for the connection to the database.

```
; Example.

[config]
host = 127.0.0.1
db = rpiPlay
userdb = webUser
passworddb = password
```

- Grant execution permission to the script **checkStatus.sh**.
 
```
chmod +x Your Path/scripts/checkStatus.sh
```

- En tu base de datos MySQL debes de ejecutar el schema.sql
**Ten en cuenta que se tienen que configurar los servicios Web i MySQL antes de configurar la RPI, en caso de que se hiciera al reves, se tendria que reiniciar la RPI para que haga la primera connexión con la BDD.**

```
mysql> SOURCE PathToFile/schema.sql;
```

## schema.sql

The database of our project is responsible for storing what the user introduces, folders, videos, users, raspberry data and schedules.

All tables are related to a user, with this we achieve independence among different users, we do not want the objects that a user creates are visible to all other users.


| usuaris | carpetes |  multimedia | horaris |  raspberries |
|----------------------------------------|------------------------------------------------------------------------|--------------------------------------------------|------------------------------------------|-------------------------------------------------------------------------------------------------------------------|
| Data of all registered users. | Data from folders that contain user videos.  | Data from videos downloaded by users.  |  Data of user-created schedules. | Data of the raspberries that we have at the disposal of the users, and the owner who has registered this raspberry.  |


| web  | rpiClient                                       |
|----------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------- |
|User with the necessary permissions required by the web portal, only permissions for the project project RPI.|User with the necessary permissions for the RPI to insert into the table raspberries, check the public ip that is currently assigned and change it if your ISP has assigned a new public IP.|


## Front-End

### login.php
Login page of the users for the web page.

### register.php
Users registration page.

### legal.html
Conditions page.

### registerRPI.php
Page that leaves after the first start, the user that has an RPI will have to register the Hardware in his account through the MAC of the RPI that is saved in the Database.

![registerRPI](https://github.com/jatorrents/Projecte/blob/master/images/registerRPI.PNG)

### home.php
Welcome page and minimum management of the RPI, from this page we can see some data of the RPI, update the scripts and restart the RPI.

![home](https://github.com/jatorrents/Projecte/blob/master/images/home.PNG)

### videos.php
On this page the user creates and deletes the folders containing the videos, here you can also select the folder that needs to play or stop videos.

- Create folder:

![newFolder](https://github.com/jatorrents/Projecte/blob/master/images/newFolderEdited.PNG)

1. Enter the name of the folder.
2. Click on the botton for create folders.

- Delete folder:

![deleteFolder](https://github.com/jatorrents/Projecte/blob/master/images/deleteFolderEdited.PNG)

1. You must display the menu.
2. Select the folder you want to delete.
3. Click on the button to delete the selected folder.

**Attention! Logically if you delete a folder, you also delete all the videos in that folder.**

-  Delete, Play and Stop videos:

![deletePlayStopVideos](https://github.com/jatorrents/Projecte/blob/master/images/playStopDeleteEdit.PNG)

1. Select the folder you want to edit.
2. Play videos from the selected folder.
3. Stop playing videos from the selected folder
4. Click on the trash button to delete the video.

### userDownload.php
Page to download the videos using the URL.

- Download videos:
First part:

![downloadVideoEdit](https://github.com/jatorrents/Projecte/blob/master/images/downloadVideoEdit.PNG)

1. Enter URL.
2. Show preview.

Second part:

![downloadVideo2Edit](https://github.com/jatorrents/Projecte/blob/master/images/downloadVideo2Edit.PNG)

1. Enter name of the video.
2. Select the folder.
3. And click the download button.

You will be redirected to videoDownload.php.

**Currently we only allow YouTube as a source.**

### videoDownload.php
This document is responsible for receiving via POST the data of the video that the user enters in the document userDownload.php, and to download the videos physically in the RPI while showing the progress on the screen.

You should wait for it to complete if you after click the download button.

![downloadVideo3Edit](https://github.com/jatorrents/Projecte/blob/master/images/downloadVideo3Edit.PNG)

### schedules.php
This page is responsible for programming video playback schedules, it does not play videos from one range to another, what it does basically is at the indicated time it starts playing the indicated folder.

- Create schedule:

![schedulesEdit](https://github.com/jatorrents/Projecte/blob/master/images/schedulesEdit.png)

1. First you name a schedule.
2. Then select the time you want to play the videos in the folder that we will select later.
3. You select the folder.
4. Programs the reproduction of the videos.
5. If you want you can delete any time by clicking on the corresponding button.

### logout.php
Basically it is the page that closes the session.

## Back-End

### dbConnection.php

Route:/dbConnection.php

The main function of this page is to be the bridge of the user with the database, only when the user tries to login or register.

##### Bottom if
The IFs are responsible for verifying Google's reCaptcha  and for passing the form data to the register (), login () functions.(Doesn't do nothing with Google in local installation)
##### register($name, $surnames, $email, $username, $password)

This function is responsible for INSERTING in the project database RPI in the user table the user data that has passed the POST of the form the document register.php.
The data in the database to log in is consulted from document db.ini.
For security and respect for the user, we can not save passwords in a plain text, so we use the password_hash ($ password, PASSWORD_BCRYPT) function to encrypt passwords before inserting them into the BDD.

```
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
```

##### login($username, $password)

As its name indicates the login () function, it is responsible for checking through the data that will guarantee us the start of the session, by checking the login data on the DB the user and the encrypted password, using the function password_verify (), this function checks that the password is the same as in the BDD, if any, a PHP session would start with the user number in question (necessary for the web to display the content according to the user's number) and the username, otherwise it does not seem to create a PHP error session and return to the beginning of the login process. If the PHP session exists, show a screen error in the login.php document indicating that the user or password is not correct.

```
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
```

### funcions.php
Route:/funcions.php

This document of the web structure is basically responsible for containing all the functions that the web page uses to obtain information.

#### Check the public IP of the Raspberry.
##### getPublicIPRPI()
Get from the database the public IP of the RPI.

```
function getIPRPI(){
    /*
   $ipRPI=listRPI()[0]["ipPublica"];
   return $ipRPI;
   */
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
```
    Output: string IP

#### Get ID YouTube video.
##### get_youtube_id($url)
Depending on the YouTube URL that the user give us as a parameter, we obtain their identifier to generate other content such as the title or thumbnail of a video.
```
function get_youtube_id($url){
    parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
    return $my_array_of_vars['v'];
}
```
    Output: string The video ID

#### Get title of YouTube video.
##### get_youtube_title($id)

This feature is used to extract the title of YouTube videos through an identifier.
 ```
 function get_youtube_title($id){
     $json = file_get_contents('http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=' . $id . '&format=json'); //get JSON video details
     $details = json_decode($json, true); //parse the JSON into an array
     return $details['title'];
 }
 ```
    Output: string The video title

#### Get used disk space percentage.
##### usedDisk()
Function to calculate the free space of a UNIX operating system through SSH, in our case the user's RPI has logged in at that time.
```
function usedDisk(){
    global $userSSH;
    global $passwordSSH;

    $disk_used_p=shell_exec("sshpass -p '$passwordSSH' ssh -oStrictHostKeyChecking=no $userSSH@".getIPRPI()." df --output=pcent / | sed 1d");
    return $disk_used_p;
}
```
    Output: string [0-100]%.

#### Aesthetic functions.
##### IfExistsPostLinkPreviewVideo()
We use this function to hide all video containers on some web pages where a "preview" is displayed, if the user passes us a link we will return a DIV element without the class w3-hide (This class what it does is hide the item).

```
function IfExistsPostLinkPreviewVideo(){
    if(isset($_POST['link'])){
        return '<div class="w3-container">';
    }
    else{
        return '<div class="w3-container w3-hide">';
    }
}
```
    Output: string <div class="w3-container (w3-hide)">

##### hideLoginError()
This function is responsible for displaying an error message on the login.php page when the credentials entered by the user are not the correct ones.
What it does is check the session PHP errorLogin if it exists, (Generated in the document dbConnection.php in the login function) add the class w3-show to the div of the login error, otherwise add the class to hide the div (although it would not be necessary for the user to have already logged in).

```
function hideLoginError(){
    //dbConnection.php $_SESSION['errorLogin']
    if($_SESSION['errorLogin']){
	unset($_SESSION["errorLogin"]);
        return "w3-show";
    }
    else{
        return "w3-hide";
    }
}
```
    Output: string w3-show|w3-hide

##### dynamicDiv($numVideos)
Depending on the number of thumbnails of the videos that we have to show on the web page we have to print them in one size or another, this function is to change the size occupied by the divs that contain the thumbnails of the videos depending entirely on the quantity.

```
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
```

    Output: w3-content|w3-half|w3-third|w3-quarter

# Exemple gràfic
![w3Content](/uploads/de6ff146828f0278776c16e219d175f6/w3Content.png)

![w3Half](/uploads/85b52c69cb996379da85de6ce5d96fd3/w3Half.png)

![w3Third](/uploads/8fd4aa08528a7c4428723a4490688dca/w3Third.png)

![w3Quarter](/uploads/b16c565e3fa52dcd34e72ac8b0fdfb5c/w3Quarter.png)

#### Create, delete or list the folders that are available to save the videos.
##### newFolder($nameFolder)
Function that is responsible for receiving by parameter the name of the folder that the user wants to create and save the data of the folder in the database, also creates the folder physically in the RPI.

To avoid problems, physically in the RPI are stored folders with the same name but when the user created a folder with spaces in the name, we have to eliminate the spaces for practical questions. The user will always see the name with spaces. Returns a warning to the user that the folder was created.

```
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
```
    Output: string
      <div class="w3-panel w3-green w3-display-container">
      <span onclick="this.parentElement.style.display=\'none\'" class="w3-button w3-green w3-large w3-display-topright">&times;</span>
  	  <h3>Carpeta creada!</h3>
  	   <p><i class="glyphicon glyphicon-ok-circle"></i> S\'ha creat la carpeta '.$nameFolder.'</p>
  	</div>

##### delFolder($nameFolder)
It is in charge of exactly the opposite of the previous function but using the same scheme, instead of creating the folder, it eliminates the folder when the user passes the name of it by parameter.
It does not return anything because the folder just disappears.

```
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

```

##### lsFolders()
Function to consult in the database all the folders that are created by the user that executes the function, since it is used for the query IdPropietario that is equal to the session PHP userID. Returns an array with the folders that are owned by the user.

```
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
```

#### Functions to select, save or delete the videos that the user downloads in the database (The delete function delVideo($nameFolder, $nameVideo) also eliminates them physically from the RPI).
##### downloadVideo($nameVideo,$nameFolder,$userID,$link)
This function is in charge of registering in the database the videos that the user decides to download through the userDownloads.php page.
```
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
```
##### showDBVideos($nameFolder)
This function is responsible for displaying videos that are registered in the database, where the location is equal to $namefolder and the idPropietario is equal to the session PHP userID, basically is usable to select the videos in a folder when the folder name is passed, with this we can show the videos that are being played or the videos in a folder that the user selects.
```
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
```
##### delVideo($nameFolder, $nameVideo)
This function is to remove a video from the database and physically in the RPI. Receive as parameters the folder and the video that has to be deleted.
```
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
```

#### Stop and Play videos.
##### playFolder($nameFolder)
Function to play all videos from one folder in loop.
```
function playFolder($nameFolder){
    global $userSSH;
    global $passwordSSH;
	session_start();
    $_SESSION['play']=$nameFolder;
    $noSpacesNameFolder = str_replace(" ","",$nameFolder);
    shell_exec("sshpass -p '$passwordSSH' ssh -oStrictHostKeyChecking=no $userSSH@".getIPRPI()." sudo /home/project/scripts/startvideo.sh stop");
    shell_exec("sshpass -p '$passwordSSH' ssh -oStrictHostKeyChecking=no $userSSH@".getIPRPI()." sudo nohup /home/project/scripts/startvideo.sh start /home/project/videos/$noSpacesNameFolder/ >/dev/null 2>/dev/null &");
}
```
##### stopFolder($nameFolder)
Function to stop playing one folder.
```
function stopFolder($nameFolder){
    global $userSSH;
    global $passwordSSH;
	session_start();
    unset($_SESSION['play']);
    shell_exec("sshpass -p '$passwordSSH' ssh -oStrictHostKeyChecking=no $userSSH@".getIPRPI()." sudo /home/project/scripts/startvideo.sh stop");
}
```
#### Check connection status with RPI
##### statusRPI()
Function to check the state of the connection with the RPI, the web server checks it by executing a bash script that does all the verification of the connection and returns the result, if there is a connection, it returns a green < button > with the text "Online" if it is negative, it returns a red < button > with the text "Offline".
```
function statusRPI(){
    global $userSSH;
    global $passwordSSH;
	$output=shell_exec("scripts/./checkStatus.sh $userSSH $passwordSSH ".getIPRPI()." 2>&1");
	return $output;
}
```
#### Consult, create or delete schedules in the BDD and in the RPI.
##### createSchedule($action,$nameSchedule,$time,$nameFolder)
Function to create a schedule, both in BDD and in the RPI.
```
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
```
##### deleteSchedule($nameSchedule)
Function to delete schedule, both in the database and in the RPI
```
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
```
##### getSchedule()
Function to know the existing schedules for a user.
```
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
```
    Output: array schedules
#### Associate a web user with an RPI (Register an RPI).
##### registerRPI($mac)
Register the RPI for a user of the db, when a user passes us the MAC of the RPI, we will always check if it exists in our database and register it in case it does not have any owner, we will return a notice that is already registered in the case that is already registered.
```
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
```
Output: string "Ja registrat" | NULL (Registers the RPI for a user of the db)
#### Obtain the data of the RPI of the users.
##### listRPI()
Function that returns the data of the RPI, property of the user that makes the query.
```
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
```
    Output: array ipPublica,numSerie,nomModel
#### Update or Restart RPI
##### updateRPI()
```
Coming soon!
```
#### restartRPI()
Function to restart the RPI when on the home.php page you click on the reset button.
```
function restartRPI(){
    global $userSSH;
    global $passwordSSH;
    if(isset($_SESSION['play'])){
        unset($_SESSION['play']);
    }
    shell_exec('sshpass -p '.$passwordSSH.' ssh -oStrictHostKeyChecking=no '.$userSSH.'@'.getIPRPI().' sudo init 6');
}
```
### checkUsername.php
This document is dedicated to checking the username passed through POST from the registry.php using Ajax every time you enter the user's name in the entry.
