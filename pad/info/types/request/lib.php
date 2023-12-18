<?php


  function padRequestStart () {

   if ( function_exists ('getallheaders') )
      $headers = getallheaders();
    else
      $headers = [];

    padInfoFile ( "request/in.json",  [
        'headers' => $headers,
        'get'     => $_GET ??    '',
        'post'    => $_POST ??   '',
        'cookies' => $_COOKIE ?? '',
        'files '  => $_FILES ?? '',
        'server'  => $_SERVER ?? '',
        'getenv'  => getenv () ?? '' , 
        'session' => $_SESSION ?? '',
        'request' => $_REQUEST ?? '' ] );

    $padRequestInput = file_get_contents('php://input') ?? '';

    if ( $padRequestInput )
      padInfoFile ( "request/in.txt",  $padRequestInput );

  }


  function padRequestEnd () {

    global $padOutput;

    $phpHeaders = headers_list ()         ?? [];
    $padHeaders = $GLOBALS ['padHeaders'] ?? [];

    foreach ( $padHeaders as $header ) {
      $key = array_search ( $header, $phpHeaders );
      if ( $key !== FALSE )
        unset ( $phpHeaders [$key] );
    }

    padInfoFile ( "request/out.json",  [
        'http'       => http_response_code () ?? 'null',
        'phpHeaders' => $phpHeaders,
        'padHeaders' => $padHeaders
      ] );

    if ( $padOutput )
      padInfoFile ( "request/out.txt",  $padOutput );

  }
  

?>