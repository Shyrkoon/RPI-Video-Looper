<?php
/**
 * Created by PhpStorm.
 * User: José
 * Date: 12/03/2018
 * Time: 12:49
 */
session_start();
unset($_SESSION['username']);
session_destroy();

header("Location: login.php");
exit;
?>
