<?php

  $padBuildNow = substr(APP, 0, -1);
  
  $padExits     = [];
  $padBuildMrg = padExplode ("pages/$page", '/');

  foreach ($padBuildMrg as $padValue) {

    $padBuildNow .= "/$padValue";

    if ( is_dir ($padBuildNow) ) {

      $padCall = "$padBuildNow/inits.php";
      include 'go.php';

      $padExits [] = "$padBuildNow/exits.php";

    }

  }

  $padCall = "$padBuildNow.php";
  $padBase [$pad] .= include 'go.php';

  foreach ( array_reverse ($padExits) as $padCall )
    include 'go.php';

  $padBase [$pad] .= padGetHtml ( APP . "pages/$page.html" );

?>