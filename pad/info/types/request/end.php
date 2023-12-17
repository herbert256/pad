<?php

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
    
?>