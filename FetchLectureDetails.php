<?php
require "CommonFiles/connection.php";
require "CommonFiles/CommonConstants.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Import Data</title>
    <?php include("CommonFiles/CommonHead.php"); ?>    
    <script src="JS/ImportData.js"></script>
    
</head>

<body style = "">    
    <?php include "CommonFiles/Menu.php"?>
    
    <div id="main_body" style="color: rgba(255, 255, 255, 0.84);padding: 20px;" >  
        <?php
            date_default_timezone_set("Asia/Kolkata");
            echo "<h3>Day: ".date("l")."</h3>";
            echo "<br/><h3>Time: ".date("h:i:s A")."</h3>";
        ?>
    </div>
    
    
    
</body>
</html>