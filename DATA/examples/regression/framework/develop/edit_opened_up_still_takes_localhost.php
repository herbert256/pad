<?php

  // $editRemote opens the editor to other machines, and $editHosts then names the hosts a
  // request may name "besides localhost, 127.0.0.1 and [::1]", as its configuration says -
  // where a request naming localhost was turned away once any host was listed. The rule is
  // asked here directly, with the two settings as an opened-up editor has them.

  include_once APPS . 'edit/_lib/api.php';

  $GLOBALS ['editRemote'] = TRUE;
  $GLOBALS ['editHosts']  = [ 'dev.example' ];

  $editKeep = $_SERVER ['HTTP_HOST'] ?? '';
  $answer   = '';

  foreach ( [ 'localhost:8080', '127.0.0.1', 'dev.example', 'other.example' ] as $editOne ) {
    $_SERVER ['HTTP_HOST'] = $editOne;
    $answer .= "$editOne " . ( editAllowed () ? 'in' : 'out' ) . ', ';
  }

  $_SERVER ['HTTP_HOST'] = $editKeep;

  unset ( $GLOBALS ['editRemote'], $GLOBALS ['editHosts'], $editKeep, $editOne );

?>
