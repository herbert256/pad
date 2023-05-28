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
  $padCallReturn = include 'call/any.php';

  if ( is_array ($padCallReturn) ) {
    $padBuildArray   = padData ( $padCallReturn);
    $padBuildArrayOB = $padCallOB;
  }
  else
    $padBase [$pad] .= $padCallReturn;    

  foreach ( array_reverse ($padExits) as $padCall )
    $padBase [$pad] .= include 'call/string.php';

?>