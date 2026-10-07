<?php

  // The globals type: search $GLOBALS for the path $names, then GET and POST through
  // padAtSpecial.
  //
  // What is searched is the application's globals: $GLOBALS without the engine's names -
  // pad*, pq* and PHP's superglobals - and with $padMailLast, the engine array TAGS.md hands
  // to templates ({$padMailLast.subject}), as the plain field search reads them
  // (lib/field/field.php). Refusing a superglobal by the path's first name was not enough:
  // a first step that is a wildcard, an ordinal or a condition resolves to a key of the
  // array searched without naming it, so {$DB_PASSWORD<>x.DB_PASSWORD} stepped into
  // $_SERVER and {$input<>x.input.password} into the engine's last fetch; with those keys
  // not in the array no step reaches them, {$_SERVER.HTTP_HOST@globals} included.

  $padGlobalsApp = array_diff_key ( $GLOBALS, padEngineNames ( $GLOBALS ) );

  if ( isset ( $GLOBALS ['padMailLast'] ) )
    $padGlobalsApp ['padMailLast'] = $GLOBALS ['padMailLast'];

  $check = padAtSearch ( $padGlobalsApp, $names, $padAtNoDeep ?? 0 );
  if ( $check !== INF )
    return $check;

  $check = padAtSpecial ( $names, $cor );
  if ( $check !== INF )
    return $check;

  return INF;

?>
