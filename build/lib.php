<?php

  $padLibData = '';
  
  $padBuildNow = substr(APP, 0, -1);
  $padBuildMrg = padExplode ("pages/$page", '/');

  foreach ($padBuildMrg as $padBuildKey => $padBuildValue) {

    $padBuildNow .= "/$padBuildValue";

    if ( is_dir ($padBuildNow) ) {

      $padCall = "$padBuildNow/_lib.php";

      $padLibData .= include 'go.php';
      $padLibData .= padGetHtml ( "$padBuildNow/_lib.html" );

    }
    
  }

?>