<?php

  $padBuildNow = substr(APP, 0, -1);

  $padExits     = [];
  $padBuildMrg = padExplode ("pages/$page", '/');

  foreach ($padBuildMrg as $padBuildKey => $padBuildValue) {

    $padBuildNow .= "/$padBuildValue";

    if ( $padBuildKey == array_key_last($padBuildMrg) 
       and (file_exists("$padBuildNow.php") or file_exists("$padBuildNow.html") ) ) {
 
      $padCall = "$padBuildNow.php";
      $padBase [$pad] .= include PAD . 'build/go.php';

    } elseif ( is_dir ($padBuildNow) ) {

      $padCall = "$padBuildNow/inits.php";
      $padBase [$pad] .= include PAD . 'build/go.php';

      $padExits [] = "$padBuildNow/exits.php";

    } else {

      $padCall = "$padBuildNow.php";
      $padBase [$pad] .= include PAD . 'build/go.php';

    }

  }

  foreach ( array_reverse ($padExits) as $padCall )
    $padBase [$pad] .= include PAD . 'build/go.php';

?>