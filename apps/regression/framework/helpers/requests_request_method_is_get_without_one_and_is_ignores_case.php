<?php

  // The method upper-cased, GET when the request has none - a command-line render - and
  // padRequestIs comparing it with a name or any of a list, in any case.

  $yes = fn ( $b ) => $b ? 'yes' : 'no';

  $m1 = padRequestMethod ();
  $i1 = $yes ( padRequestIs ( 'get' ) ) . ' ' . $yes ( padRequestIs ( [ 'POST', 'put' ] ) ) . ' ' . $yes ( padRequestIs ( 'post, Get' ) ) . ' ' . $yes ( padRequestIs ( '' ) );

  $padKeepMethod = $_SERVER ['REQUEST_METHOD'];

  $_SERVER ['REQUEST_METHOD'] = 'patch';
  $m2 = padRequestMethod () . ' ' . $yes ( padRequestIs ( [ 'PUT', 'PATCH' ] ) );

  unset ( $_SERVER ['REQUEST_METHOD'] );
  $m3 = padRequestMethod ();

  $_SERVER ['REQUEST_METHOD'] = $padKeepMethod;

?>
