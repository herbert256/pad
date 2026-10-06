<?php

  // The globals type: search $GLOBALS for the path $names, then GET and POST through
  // padAtSpecial.
  //
  // The path's first name steps straight into a key of $GLOBALS, PHP's own superglobals
  // among them, so {$_SERVER.HTTP_HOST@globals} walked into $_SERVER and answered the host
  // header; the plain field search reads none of those (lib/field/level.php), and this reads
  // them no more. Only a name of a superglobal is refused - they are the _ arrays and
  // GLOBALS - while an application global and an engine one such as $padMailLast, which
  // templates read as the field search lets them, still search $GLOBALS.

  $padGlobalsFirst = (string) reset ( $names );

  if ( ! str_starts_with ( $padGlobalsFirst, '_' ) and $padGlobalsFirst !== 'GLOBALS' ) {
    $check = padAtSearch ( $GLOBALS, $names );
    if ( $check !== INF )
      return $check;
  }

  $check = padAtSpecial ( $names, $cor );
  if ( $check !== INF )
    return $check;

  return INF;

?>
