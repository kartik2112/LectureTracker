<?php


require "CommonFiles/connection.php";
require "CommonFiles/CommonConstants.php";

$uploadFlagCourses=FALSE;
$uploadFlagUsers=FALSE;
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
                $uploadFlagCourses=TRUE;
                $uploadFlagUsers=TRUE;

            } else {
                $errorMessage.="Sorry, there was an error uploading your file.";
            }

        }
        else{
            if (move_uploaded_file($_FILES["ExcelFileUpload"]["tmp_name"], $fileLocation." ".date("d-m-Y").".".$fileExtension)) {
                $successMessage.="The file ". basename( $_FILES["ExcelFileUpload"]["name"]). " has been uploaded.<br/>";
                $fileLocation=$fileLocation." ".date("d-m-Y").".".$fileExtension;
                $uploadFlagCourses=TRUE;
                $uploadFlagUsers=TRUE;

            } else {
                $errorMessage.="Sorry, there was an error uploading your file.<br/>";
            }
        }
        
    }
    else{
        $errorMessage.="Incorrect File Type<br/>";
    }
}

if($uploadFlagCourses && $uploadFlagUsers){
                

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
                        $successMessage.='Detected sheet '.$sheetname.' of file '.$_FILES["ExcelFileUpload"]["name"].'<br/>';    //pathinfo($fileLocation,PATHINFO_BASENAME) exact name of file saved finally  
        
                        $sheetCoursesData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
                
                        if($sheetCoursesData[1]['A']=="Semester" && $sheetCoursesData[1]['B']=="Subject" && $sheetCoursesData[1]['C']=="Teacher" && $sheetCoursesData[1]['D']=="Capacity"){
                            $uploadFlagCourses=TRUE;
                            $noOfCourses=count($sheetCoursesData)-1;
                            $successMessage.='Headings found to be correct in sheet '.$sheetname.' of file.<br/>';
                        }
                        else{
                            $errorMessage.="Incorrect Headings provided in excel sheet ".$sheetname."!<br/>";
                            $uploadFlagCourses=FALSE;
                        }
                }
                else{
                    $errorMessage.="TT sheet not present.<br/>";
                    $uploadFlagCourses=FALSE;
                }
                
}
?>

<!DOCTYPE html> <!-- for HTML 5 -->
<html>
<head>
    <title>Import Data</title>
    <?php include("CommonFiles/CommonHead.php"); ?>    
    <script src="JS/ImportData.js"></script>
    
</head>

<body style = "">    
    <?php include "CommonFiles/Menu.php"?>
    
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