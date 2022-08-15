<?php

  $padBuild_now = substr(APP, 0, -1);

  $padExits     = [];
  $padBuild_mrg = pExplode ("pages/$page", '/');

  foreach ($padBuild_mrg as $padBuild_key => $padBuild_value) {

    $padBuild_now .= "/$padBuild_value";

    if ( $padBuild_key == array_key_last($padBuild_mrg) 
       and (file_exists("$padBuild_now.php") or file_exists("$padBuild_now.html") ) ) {
 
      $padCall = "$padBuild_now.php";
      $padBase [$pad] .= include PAD . 'level/call.php';

    } elseif ( is_dir ($padBuild_now) ) {

      $padCall = "$padBuild_now/inits.php";
      $padBase [$pad] .= include PAD . 'level/call.php';

      $padExits [] = "$padBuild_now/exits.php";

    } else {

      $padCall = "$padBuild_now.php";
      $padBase [$pad] .= include PAD . 'level/call.php';

    }

  }

  foreach ( array_reverse ($padExits) as $padCall )
    $padBase [$pad] .= include PAD . 'level/call.php';

?>