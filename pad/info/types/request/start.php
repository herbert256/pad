<?php

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
  
?>