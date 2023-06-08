<?php

  if ( $name ) {

    if ( ! isset ( $GLOBALS [$name] ) )
      return INF;

    return padAtSearch ( $GLOBALS [$name], $names );

  }

  $check = padAtSearch ( $GLOBALS, $names );
  if ( $check <> INF )
    return $check;

  $check = padAnyNamesFind ( $GLOBALS, $names );
  if ( $check !== INF )
    return $check;

 return INF;

?>