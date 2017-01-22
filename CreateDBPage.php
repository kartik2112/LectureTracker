<?php
$servername="localhost";
$username="root";
$password="kkksss333";
$dbname="dbLT"; 


$conn=mysqli_connect($servername,$username,$password);
if(mysqli_connect_error()){
   die("Error connecting server");
}
$sql="create database ".$dbname;
if(mysqli_query($conn,$sql)){
   echo "Database creation successful<br/>";

}
else{
   die("Error creating database. ".mysqli_error($conn));
}

require "CommonFiles/connection.php";
require "CommonFiles/CommonConstants.php";

/*$sql="create table Subject(Subj_ID int AUTO_INCREMENT,Subject_Name varchar(".SUBJ_NAME_LENGTH."), Sem int,File_Link varchar(".FILE_LINK_LENGTH."),Capacity int,Teacher varchar(".TEACHER_NAME_LENGTH."),imagelink varchar(".IMAGELINK_LENGTH."),primary key(Subj_ID))";
if(mysqli_query($conn,$sql)){
    echo "Subject table created successfully<br/>";
}
else{
    echo "Error while creating Subject table ".mysqli_error($conn)."<br/>";
}*/




?>

