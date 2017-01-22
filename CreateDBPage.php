<?php
$servername="localhost";
$username="root";
$password="kkksss333";
$dbname="dbLT"; 


/*$conn=mysqli_connect($servername,$username,$password);
if(mysqli_connect_error()){
   die("Error connecting server");
}
$sql="create database ".$dbname;
if(mysqli_query($conn,$sql)){
   echo "Database creation successful<br/>";

}
else{
   die("Error creating database. ".mysqli_error($conn));
}*/

require "CommonFiles/connection.php";
require "CommonFiles/CommonConstants.php";

$sql="create table Subject(Subj_ID int AUTO_INCREMENT, Subject_ShortForm varchar(".SUBJ_NAME_LENGTH."), Subject_Name varchar(".SUBJ_NAME_LENGTH."),primary key(Subj_ID))";
if(mysqli_query($conn,$sql)){
    echo "Subject table created successfully<br/>";
}
else{
    echo "Error while creating Subject table ".mysqli_error($conn)."<br/>";
}


$sql="create table TimeTableEvent(Day varchar(20), Start_Time varchar(12), End_Time varchar(12), Subj_ID int, Type varchar(2), Room_No varchar(10), Teacher_Initials varchar(10), primary key(Day,Start_Time))";
if(mysqli_query($conn,$sql)){
    echo "TimeTableEvent table created successfully<br/>";
}
else{
    echo "Error while creating TimeTableEvent table ".mysqli_error($conn)."<br/>";
}



$sql="alter table TimeTableEvent add constraint TimeTableEvent_Key1 foreign key(Subj_ID) references Subject(Subj_ID)";
if(mysqli_query($conn,$sql)){
    echo "TimeTableEvent foreign key created successfully<br>";
}
else{
    echo"Error while creating TimeTableEvent foreign key. ".mysqli_error($conn)."<br/>";
}


?>

