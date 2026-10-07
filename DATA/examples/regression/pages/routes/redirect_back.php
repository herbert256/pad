<?php

  // padRedirect () with no page goes back to the name the page was asked by - and that name
  // goes into the address encoded again: a segment a route bound may hold what an address
  // reads as its own, and went in as it was - a&b came back as routes/back/a with a value
  // b, c#d as routes/back/c, e%f with a broken escape.

  function routesBack ( $name ) {

    global $padHost;

    $r = padCurl ( [ 'url'     => $padHost . 'regression/pages/?routes/back/' . rawurlencode ( $name ) . '&again&padInclude',
                     'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );

    return $r ['result'] . ' ' . preg_replace ( '#^.*/regression/pages/#', '', $r ['info'] ['redirect_url'] ?? '' );

  }

  $backResult = implode ( ' | ', [ routesBack ( 'plain' ), routesBack ( 'a&b' ), routesBack ( 'c#d' ), routesBack ( 'e%f' ) ] );

?>
