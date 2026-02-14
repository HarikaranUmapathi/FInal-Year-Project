<?php
$host="localhost";
$user="root";
$password="root123";    
$db="users_detail";

$conn=mysqli_connect($host,$user,$password,$db);

if(!$conn){
    die("Connection failed: ".mysqli_connect_error());
}
?>