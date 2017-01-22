<?php
$servername="localhost";
$username="root";
$password="kkksss333";
$dbname="dbLT";

$conn=mysqli_connect($servername,$username,$password,$dbname);
if(mysqli_connect_error()){
    die("Cannot access db ".mysqli_error($conn));
    
}
?>
