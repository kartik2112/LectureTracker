<?php
require "CommonFiles/connection.php";
require "CommonFiles/CommonConstants.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Current Lecture</title>
    <?php include("CommonFiles/CommonHead.php"); ?>
    
</head>

<body style = "">    
    <?php include "CommonFiles/Menu.php"?>
    
    <div id="main_body" style="color: rgba(255, 255, 255, 0.84);padding: 20px;" >  
        <?php
            date_default_timezone_set("Asia/Kolkata");
            echo "<h5 style='text-align:center;'>Day: ".date("l")."</h3>";
            echo "<br/><h4 style='text-align:center;'>Time: ".date("h:i:s A")."</h3>";

        ?>
        <div class="row">
            <div class="col s12 m8 offset-m2 l6 offset-l3" style="">
                <div class="card z-depth-2" style="text-align: center;margin: auto;padding: 20px;color: #000;">
                        <?php
                            $sqlFindSubj="select * from TimeTableEvent where Start_Time<='".date("h:i:s A")."' and End_Time>='".date("h:i:s A")."' and Day='".date("l")."'";
                            $result=mysqli_query($conn,$sqlFindSubj);
                            if(mysqli_num_rows($result)==1){
                                $rowEvent=mysqli_fetch_array($result,MYSQLI_ASSOC);
                                $sqlSubjName="select Subject_Name from Subject where Subj_ID='".$rowEvent['Subj_ID']."'";
                                $resultSubjName=mysqli_query($conn,$sqlSubjName);
                                $rowSubjName=mysqli_fetch_array($resultSubjName,MYSQLI_ASSOC);
                                echo "<h4>Lecture currently being conducted</h4>";
                                echo "<b>Lecture:</b> ".$rowSubjName['Subject_Name']."<br/>";
                                echo "<b>Room No:</b> ".$rowEvent['Room_No']."<br/>";
                                echo "<b>Type:</b> ".$rowEvent['Type']."<br/>";
                                echo "<b>Teacher:</b> ".$rowEvent['Teacher_Initials']."<br/>";
                                
                            }
                            else{
                                echo "No lecture being conducted right now!";
                            }
                        ?>
                </div>
            </div>
        </div>
    </div>
    
    
    
</body>
</html>