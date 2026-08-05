<?php

  // The globals type: search $GLOBALS for the path $names, then the request superglobals
  // ($_POST, $_GET, $_SESSION and friends) through padAtSpecial.

  $check = padAtSearch ( $GLOBALS, $names );
  if ( $check !== INF )
    return $check;

  $check = padAtSpecial ( $names, $cor );
  if ( $check !== INF )
    return $check;

  return INF;

?>