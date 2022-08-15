<?php

  $padBuild_now = substr(APP, 0, -1);
  
  $padExits     = [];
  $padBuild_mrg = padExplode ("pages/$page", '/');

  foreach ($padBuild_mrg as $padValue) {

    $padBuild_now .= "/$padValue";

    if ( is_dir ($padBuild_now) ) {

      $padCall = "$padBuild_now/inits.php";
      include PAD . 'level/call.php';

      $padExits [] = "$padBuild_now/exits.php";

    }

  }

  $padCall = "$padBuild_now.php";
  $padBase [$pad] .= include PAD . 'level/call.php';

  foreach ( array_reverse ($padExits) as $padCall )
    include PAD . 'level/call.php';

  $padBase [$pad] .= padGetHtml ( APP . "pages/$page.html" );

?>