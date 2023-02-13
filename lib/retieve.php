<?php


  function padRetievePage ( $padRetrievePage,  $padRetrieveMode='include', $padRetrieveMerge='content' ) {

    include PAD . 'retrieve/inits.php'; 

    $page          = $padRetrievePage;
    $padBuildMode  = $padRetrieveMode;
    $padBuildMerge = $padRetrieveMerge;

    $padInclude = TRUE;

    include PAD . 'build/build.php'; 

    $padHtml [$pad] = $padBase [$pad];    

    return include PAD . 'retrieve/exits.php'; 
 
  }


  function padRetrieveContent ( $padRetrieveContent ) {

    include PAD . 'retrieve/inits.php'; 

    $padHtml [$pad] = $padRetrieveContent;    

    return include PAD . 'retrieve/exits.php'; 

  }


  function padTagAsFunction ($tag, $parms) {

    return padRetrieveContent ( '{' . $tag . ' ' . $parms . '}' );

  }


?>