<?php

  $padBuildNow = substr(APP, 0, -1);  

  foreach ($padBuildMrg as $padBuildKey => $padBuildValue) {

    $padBuildNow .= "/$padBuildValue";

    if ( is_dir ($padBuildNow) ) {

      $padCall = "$padBuildNow/_lib.php";
      $padBase [$pad] .= include 'go.php';

      $padBase [$pad] .= padGetHtml ( "$padBuildNow/_lib.html" );

    }
    
  }

?>