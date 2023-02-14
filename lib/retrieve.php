<?php


  function padPageInclude ( $page ) {

    return padGetHtml ( APP . "pages/$page.html" , TRUE );

  }

  function padPageGet ( $page, $parms ) {

    $query = '';
    foreach ( $parms as $padK => $padV )
      $query .= "&$padK=" . urlencode($padV);

    return pad ( $GLOBALS['app'], $page, $query, '1' );

  }

  function padPageFunction ( $padRetrievePage, $padRetrieveParms=[] ) {

    include PAD . 'retrieve/inits.php'; 

    $page          = $padRetrievePage;
    $padBuildMode  = 'include';
    $padBuildMerge = 'content';

    foreach ( get_defined_vars() as $padK => $padV )
      $$padK = $padV;
    include PAD . 'build/build.php'; 

    $padData [$pad]     = [];
    $padData [$pad] [1] = [];

    foreach ( get_defined_vars() as $padK => $padV )
      if ( padValidStore($padK) )
        $padData [$pad] [1] [$padK] = $padV;

    foreach ( $padRetrieveParms as $padK => $padV )
      $padData [$pad] [1] [$padK] = $padV;

    if ( count ( $padData [$pad] [1] ) ) {
      $padKey     [$pad] = 1;
      $padCurrent [$pad] = $padData [$pad] [1];
    } else
      $padData [$pad] = padDefaultData ();

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