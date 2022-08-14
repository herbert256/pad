<?php

  $pBuild_now = substr(APP, 0, -1);
  
  $pExits     = [];
  $pBuild_mrg = pExplode ("pages/$page", '/');

  foreach ($pBuild_mrg as $pValue) {

    $pBuild_now .= "/$pValue";

    if ( is_dir ($pBuild_now) ) {

      $pCall = "$pBuild_now/inits.php";
      include PAD . 'level/call.php';

      $pExits [] = "$pBuild_now/exits.php";

    }

  }

  $pCall = "$pBuild_now.php";
  $pBase [$p] .= include PAD . 'level/call.php';

  foreach ( array_reverse ($pExits) as $pCall )
    include PAD . 'level/call.php';

  $pBase [$p] .= pGet_html ( APP . "pages/$page.html" );

?>