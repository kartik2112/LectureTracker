<?php
    session_start();
    require_once "CommonFiles/connection.php";
    require "CommonFiles/CommonConstants.php";    
    
?>
<div class="navbar-fixed">
    <nav>
        <div class="nav-wrapper">
            <a href="index.php" class="brand-logo" style="margin-left: 20px">Lectures tracker</a>
            <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down">
                    <li id="MenuLoginLI"><a id="MenuLogin" class="tooltipped" data-position="bottom" data-delay="100" data-tooltip="Log in here" href="UploadExcel.php" >UPLOAD EXCEL</a></li>                    
                    
            </ul>
            <ul class="side-nav" id="mobile-demo">
                <li id="SideMenuLoginLI"><a id="SideMenuLogin" class="tooltipped" data-position="right" data-delay="500" data-tooltip="Log in here" href="UploadExcel.php">UPLOAD EXCEL</a></li>
                                       
            </ul>
        </div>
    </nav>
    
    
   
         
</div>


<script>
    $(document).ready(function () {
        $(".button-collapse").sideNav();
        $(".dropdown-button").dropdown();
    });
</script>
<br/>
<br/>