<?php

  $padExits    = [];
  $padBuildNow = substr(APP, 0, -1);  

  foreach ($padBuildMrg as $padValue) {

    $padBuildNow .= "/$padValue";

    if ( is_dir ($padBuildNow) ) {

      $padCall = "$padBuildNow/inits.php";
      $padBase [$pad] .= include 'go.php';

      $padExits [] = "$padBuildNow/exits.php";

    }

  }

  $padCall = "$padBuildNow.php";
  $padBase [$pad] .= include 'go.php';

  foreach ( array_reverse ($padExits) as $padCall )
    $padBase [$pad] .= include 'go.php';

?>