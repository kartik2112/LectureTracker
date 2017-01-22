<?php
    
require "CommonFiles/connection.php";
require "CommonFiles/CommonConstants.php";

$uploadFlagTT=FALSE;
$successMessage="";
$errorMessage="";
if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['Submit'])){
    $fileLocation="UploadedDocs/".basename($_FILES['ExcelFileUpload']['name']);
    $fileExtension=pathinfo($fileLocation,PATHINFO_EXTENSION);
    if($fileExtension=="xls" || $fileExtension=="xlsx"){
        

        //Add more error checking of contents here

        $fileLocation=substr($fileLocation,0,strripos($fileLocation,"."));
        if(file_exists($fileLocation." ".date("d-m-Y").".".$fileExtension)){
            $suffix=1;
            
            while(file_exists($fileLocation." ".date("d-m-Y")." ".$suffix.".".$fileExtension)){
                $suffix++;
            }

            if (move_uploaded_file($_FILES["ExcelFileUpload"]["tmp_name"], $fileLocation." ".date("d-m-Y")." ".$suffix.".".$fileExtension)) {
                $successMessage.="The file ". basename( $_FILES["ExcelFileUpload"]["name"] ). " has been uploaded.<br/>";
                $fileLocation=$fileLocation." ".date("d-m-Y")." ".$suffix.".".$fileExtension;
                $uploadFlagTT=TRUE;

            } else {
                $errorMessage.="Sorry, there was an error uploading your file.";
            }

        }
        else{
            if (move_uploaded_file($_FILES["ExcelFileUpload"]["tmp_name"], $fileLocation." ".date("d-m-Y").".".$fileExtension)) {
                $successMessage.="The file ". basename( $_FILES["ExcelFileUpload"]["name"]). " has been uploaded.<br/>";
                $fileLocation=$fileLocation." ".date("d-m-Y").".".$fileExtension;
                $uploadFlagTT=TRUE;

            } else {
                $errorMessage.="Sorry, there was an error uploading your file.<br/>";
            }
        }
        
    }
    else{
        $errorMessage.="Incorrect File Type<br/>";
    }
}

if($uploadFlagTT){
                

                /**
                * This part is directly taken from the examples given for PHPExcel
                * It is just tailored to the application. 
                * The basic loading techniques have been used as given in examples.
                */
                error_reporting(E_ALL);
                set_time_limit(0);
                      
                date_default_timezone_set('Asia/Kolkata');
                set_include_path(get_include_path() . PATH_SEPARATOR . 'PHPExcel-1.8/Classes/');
                include 'PHPExcel/IOFactory.php';
                    
                $inputFileType = PHPExcel_IOFactory::identify($fileLocation);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);

                /**
                * This will load all sheet names and store in array $loadedSheetNames
                */
                $objReader->setLoadAllSheets();
                $objPHPExcel = $objReader->load($fileLocation);
                $loadedSheetNames = $objPHPExcel->getSheetNames();

                

                /**
                * This part loads only the Courses sheet of the excel file and parses it
                */

                $sheetname = 'TT';
                if(in_array($sheetname,$loadedSheetNames)){
                        $objReader->setLoadSheetsOnly($sheetname);
                        $objPHPExcel = $objReader->load($fileLocation);
                        $successMessage.='Detected sheet '.$sheetname.' of file '.$_FILES["ExcelFileUpload"]["name"].'<br/>';
        
                        //$sheetCoursesData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
                
                        if( $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 0,2 )->getCalculatedValue()=="SubjID" && $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 1,2 )->getCalculatedValue()=="ShortForm" 
                            && $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 2,2 )->getCalculatedValue()=="Subject Name" )
                        {
                            if( $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 4,2 )->getCalculatedValue()=="Time" 
                                && $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 4,3 )->getCalculatedValue()=="Start Time"
                                && $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 5,3 )->getCalculatedValue()=="End Time" )                            
                            {

                                $input_string=$objPHPExcel->getActiveSheet()->getHighestColumn();

                                $base_value = 64;
                                $decimal_value = 26;
                                $maxColumns = 0;
                                for ($i = 0; $i < strlen($input_string); $i++) {
                                    $char_value = ord($input_string[$i]);
                                    $char_value -= $base_value;
                                    $char_value *= pow($decimal_value, (strlen($input_string) - ($i + 1)));
                                    $maxColumns += $char_value;
                                }
                                //Reference: http://stackoverflow.com/a/4564410/5370202

                                $days=["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
                                for($i=6;$i<$maxColumns;$i+=4){
                                    if(! ( $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $i,2 )->getCalculatedValue()==$days[($i-6)/4] && $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $i,3 )->getCalculatedValue()=="Lecture" && $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( ($i+1),3 )->getCalculatedValue()=="Type" && $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( ($i+2),3 )->getCalculatedValue()=="Teacher" && $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( ($i+3),3 )->getCalculatedValue()=="Room" ) ){
                                        $errorMessage.='Headings found to be incorrect in sheet '.$sheetname.' of file. '.$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $i,2 )->getCalculatedValue().' '.$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $i,3 )->getCalculatedValue().' '.$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( ($i+1),3 )->getCalculatedValue().' <br/>';
                                        $uploadFlagTT=FALSE;
                                    }
                                }
                                if($uploadFlagTT){
                                    $uploadFlagTT=TRUE;
                                    $successMessage.='Headings found to be correct in sheet '.$sheetname.' of file.<br/>';
                                    $maxRows=$objPHPExcel->getActiveSheet()->getHighestRow();
                                    $addSubjFail=FALSE;
                                    for($i=3;$i<=$maxRows;$i++){
                                        $id=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 0,$i )->getCalculatedValue();
                                        $subjShortForm=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 1,$i )->getCalculatedValue();
                                        $subjName=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 2,$i )->getCalculatedValue();
                                        if(isset($id) && $id!=NULL && $id!=""){
                                            $sqlSubj="insert into Subject(Subj_ID,Subject_ShortForm,Subject_Name) values($id,'".$subjShortForm."','".$subjName."')";
                                            if(!mysqli_query($conn,$sqlSubj)){
                                                $errorMessage.="Error while inserting Subject Data of row $i . ".mysqli_real_escape_string($conn,mysqli_error($conn));
                                                $addSubjFail=TRUE;
                                                break;
                                            }
                                        }
                                        else{
                                            break;
                                        }
                                        
                                    }
                                    
                                    if(TRUE){
                                        
                                        $days=["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
                                        for($col=6;$col<$maxColumns;$col+=4){
                                            for($row=4;$row<$maxRows;$row++){
                                                $startTime=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 4, $row)->getCalculatedValue();
                                                $endTime=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( 5, $row)->getCalculatedValue();
                                                $day=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, 2)->getCalculatedValue();

                                                if (!$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->isInMergeRange() || $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->isMergeRangeValueCell()) {
                                                    // Cell is not merged cell
                                                                                                        
                                                    $lectr=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $row)->getCalculatedValue();
                                                    $type=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( ($col+1), $row)->getCalculatedValue();
                                                    $teacher=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( ($col+2), $row)->getCalculatedValue();
                                                    $room=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow( ($col+3), $row)->getCalculatedValue();

                                                    //$sheetBooksData[$row][$col] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow( $col, $row )->getCalculatedValue();
                                            
                                                    $referenceRow[$col]=$lectr;
                                                    $referenceRow[$col+1]=$type;
                                                    $referenceRow[$col+2]=$teacher;
                                                    $referenceRow[$col+3]=$room;

                                                    //This will store the value of cell in $referenceRow so that if the next row is merged then it will use this value for the attribute
                                                } else {
                                                    // Cell is part of a merge-range

                                                    $lectr=$referenceRow[$col];
                                                    $type=$referenceRow[$col+1];
                                                    $teacher=$referenceRow[$col+2];
                                                    $room=$referenceRow[$col+3];

                                                    //The value stored for this column in $referenceRow in one of the previous iterations is the value of the merged cell
                                                }
                                                

                                                if(isset($startTime) && $startTime!=NULL && $startTime!="" && isset($lectr) && $lectr!=NULL && $lectr!="" ){
                                                    $sqlGetSubjID="select Subj_ID from Subject where Subject_ShortForm='".$lectr."'";
                                                    $resultGetSubjID=mysqli_query($conn,$sqlGetSubjID);
                                                    if(mysqli_num_rows($resultGetSubjID)!=0){
                                                        $rowSubjID=mysqli_fetch_array($resultGetSubjID,MYSQLI_ASSOC);
                                                        $sqlInsertTTEvent="insert into TimeTableEvent(Day,Start_Time,End_Time,Subj_ID,Type,Room_No,Teacher_Initials) ".
                                                            "values('".$day."','".date("h:i:s A",strtotime($startTime))."','".date("h:i:s A",strtotime($endTime))."',".$rowSubjID['Subj_ID'].",'".$type."','".$room."','".$teacher."')";
                                                        if(!mysqli_query($conn,$sqlInsertTTEvent)){
                                                            $errorMessage.="Error while inserting Event Data of day $day, startTime $startTime . ".mysqli_real_escape_string($conn,mysqli_error($conn));
                                                        }
                                                    }
                                                }
                                            }
                                            
                                        }
                                    }
                                }
                                
                            }
                            else{
                                $errorMessage.='Headings found to be incorrect in sheet '.$sheetname.' of file.<br/>';
                            }
                            
                        }
                        else{
                            $errorMessage.="Incorrect Headings provided in excel sheet ".$sheetname."!<br/>";
                            $uploadFlagTT=FALSE;
                        }
                }
                else{
                    $errorMessage.="TT sheet not present.<br/>";
                    $uploadFlagTT=FALSE;
                }
                
}
?>

<!DOCTYPE html> <!-- for HTML 5 -->
<html>
<head>
    <title>Import Data</title>
    <?php include("CommonFiles/CommonHead.php"); ?>    
    
    
</head>

<body style = "">    
    <?php include "CommonFiles/Menu.php"?>

    <script>
    $(document).ready(function () {
            //$("#SideMenuImportLI").addClass("active");

            <?php
                if($successMessage){
                    $splitSuccessMessage=explode("<br/>",$successMessage);
                    $timerToToast=0;
                    foreach($splitSuccessMessage as $displaySuccessMessage){
                        if($displaySuccessMessage!=""){
                            echo 'window.setTimeout(function(){';
                            echo        "var \$toastContent = \$('<span>$displaySuccessMessage</span>');";
                            echo        "Materialize.toast(\$toastContent, 7000);";
                            echo '},'.($timerToToast*1000).');';
                        }        
                        $timerToToast++;                
                    }
                    
                }

                if($errorMessage){
                    $splitErrorMessage=explode("<br/>",$errorMessage);
                    $timerToToast=1;
                    foreach($splitErrorMessage as $displayErrorMessage){
                        if($displayErrorMessage!=""){
                            echo 'window.setTimeout(function(){';
                            echo        "var \$toastContent = \$('<span>$displayErrorMessage</span>');";
                            echo        "Materialize.toast(\$toastContent, 6000);";
                            echo '},'.($timerToToast*1000).');';
                        }  
                        $timerToToast++;                
                    }
                    
                }
            ?>

        });
    </script>

    <div id="main_body" style="color: rgba(255, 255, 255, 0.84);padding: 20px;" >  
        <form class="col s6" action="" method = "POST" enctype = "multipart/form-data" onsubmit="return VerifyNUploadThisFile();">
            <div class="column" style="width: 50%;margin: auto;">
                <div class="file-field input-field">
					<div class="btn Modal_Main_inputs" style="display: block;">
                        <span>SELECT EXCEL FILE</span>
                        <input type="file" id="ExcelFileUpload" name="ExcelFileUpload" onchange="VerifyFileType()">
                    </div>
                    <div class="file-path-wrapper" style="display: block;">
                        <input class="file-path" type="text" class="Modal_Main_inputs" id="ExcelFileUploadName">
                    </div>
                </div>                
                <br/><br/>
                <button class="btn waves-effect waves-light UploadBtns" type="submit" name="Submit" style="margin: auto" value="Upload">Upload
				    <i class="material-icons right">file_upload</i>
				</button>
                    
            </div>
        </form>
    </div>
    
    
    
</body>
</html>