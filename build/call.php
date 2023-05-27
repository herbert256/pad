<?php

  $padExits    = [];
  $padBuildNow = $padBuildBas;  

  foreach ($padBuildMrg as $padValue) {

    $padBuildNow .= "/$padValue";

    if ( is_dir ($padBuildNow) ) {

      $padCall = "$padBuildNow/_inits.php";
      $padBase [$pad] .= include 'call/string.php';

      $padExits [] = "$padBuildNow/_exits.php";

    }

  }

  $padCall = "$padBuildNow.php";
  $padBase [$pad] .= include 'call/string.php';

  foreach ( array_reverse ($padExits) as $padCall )
    $padBase [$pad] .= include 'call/string.php';

?>