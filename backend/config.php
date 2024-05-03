<?php

$host = "localhost";
$user = "xbercikf";
$pass = "10,databazovejsich,hesiel";
$db = "finalApp";


$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn){
    die("DIE". mysqli_connect_error());
}