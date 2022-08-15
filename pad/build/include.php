<?php

  $padBuildNow = substr(APP, 0, -1);
  
  $padExits     = [];
  $padBuildMrg = padExplode ("pages/$page", '/');

  foreach ($padBuildMrg as $padValue) {

    $padBuildNow .= "/$padValue";

    if ( is_dir ($padBuildNow) ) {

      $padCall = "$padBuildNow/inits.php";
      include PAD . 'level/call.php';

      $padExits [] = "$padBuildNow/exits.php";

    }

  }

  $padCall = "$padBuildNow.php";
  $padBase [$pad] .= include PAD . 'level/call.php';

  foreach ( array_reverse ($padExits) as $padCall )
    include PAD . 'level/call.php';

  $padBase [$pad] .= padGetHtml ( APP . "pages/$page.html" );

?>