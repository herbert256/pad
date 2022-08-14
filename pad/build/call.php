<?php

  $pBuild_now = substr(APP, 0, -1);

  $pExits     = [];
  $pBuild_mrg = pExplode ("pages/$page", '/');

  foreach ($pBuild_mrg as $pBuild_key => $pBuild_value) {

    $pBuild_now .= "/$pBuild_value";

    if ( $pBuild_key == array_key_last($pBuild_mrg) 
       and (file_exists("$pBuild_now.php") or file_exists("$pBuild_now.html") ) ) {
 
      $pCall = "$pBuild_now.php";
      $pBase [$p] .= include PAD . 'level/call.php';

    } elseif ( is_dir ($pBuild_now) ) {

      $pCall = "$pBuild_now/inits.php";
      $pBase [$p] .= include PAD . 'level/call.php';

      $pExits [] = "$pBuild_now/exits.php";

    } else {

      $pCall = "$pBuild_now.php";
      $pBase [$p] .= include PAD . 'level/call.php';

    }

  }

  foreach ( array_reverse ($pExits) as $pCall )
    $pBase [$p] .= include PAD . 'level/call.php';

?>