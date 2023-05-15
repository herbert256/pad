<?php

  $padBuildNow = $padBuildBas;  

  foreach ($padBuildMrg as $padBuildKey => $padBuildValue) {

    $padBuildNow .= "/$padBuildValue";

    if ( is_dir ($padBuildNow) ) {

      if ( padExists ("$padBuildNow/_lib.php") )
        include_once "$padBuildNow/_lib.php";

      $padBase [$pad] .= padGetHtml ( "$padBuildNow/_lib.html", FALSE );
 
    }
    
  }

?>