<?php

  $padBuildNow = substr(APP, 0, -1);
  $padBuildCfg = padExplode ("pages/$page", '/');

  foreach ($padBuildCfg as $padBuildKey => $padBuildValue) {

    $padBuildNow .= "/$padBuildValue";

    if ( is_dir ($padBuildNow) ) {
      $padCall = "$padBuildNow/config.php";
      include 'go.php';
    }

  }

?>