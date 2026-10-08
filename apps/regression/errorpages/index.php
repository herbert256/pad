<?php

  // Fetches the answers this application gives when a request cannot go on, and shows for
  // each its status, its content type and its body: the 404 of a page that is not there -
  // also of ?sitemap.xml and ?up, which the engine would answer were they on - and of
  // _errors/404 itself, which is no page; the guard's 403, a post without its CSRF token,
  // padAbort's 429 and 410 through _errors/4xx.pad, the 500 of a failing page under the
  // 'pad' error action (diagnostics off: a visitor's request), and the 418 whose page fails
  // in turn and falls back to the plain line.

  $probes = [];

  foreach ( [ 'nothere', 'deep/not/here', 'sitemap.xml', 'up', '_errors/404', 'admin/panel',
              'slow', 'gone', 'teapot', 'broken', 'about&padInclude', 'post' ] as $ask ) {

    if ( $ask == 'post' )
      $curl = padCurl ( [ 'url' => $padHost . 'regression/errorpages/?about', 'post' => [ 'x' => '1' ] ] );
    else
      $curl = padCurl ( $padHost . "regression/errorpages/?$ask" );

    $probes [] = [
      'ask'    => $ask,
      'status' => $curl ['result'],
      'type'   => $curl ['headers'] ['Content-Type'] ?? '',
      'body'   => preg_replace ( '/Quote \S+ when/', 'Quote ID when', trim ( $curl ['data'] ) )
    ];

  }

?>
