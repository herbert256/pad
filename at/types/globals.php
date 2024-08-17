<?php

  $check = padAtSearch ( $GLOBALS, $names );
  if ( $check !== INF )
    return $check;

  $check = padAtSpecial ( $names, $cor );
  if ( $check !== INF )
    return $check;

  return INF;

?>