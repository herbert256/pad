<?php

  $padBuildNow = substr(APP, 0, -1);  

  foreach ($padBuildMrg as $padBuildKey => $padBuildValue) {

    $padBuildNow .= "/$padBuildValue";

    if ( is_dir ($padBuildNow) )
      $padBase [$pad] .= padGetHtml ( "$padBuildNow/_lib.html", TRUE );
    
  }

?>