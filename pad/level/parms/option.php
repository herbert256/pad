<?php

  // Stores a named option - {tag name='x'} or the bare {tag optional} form, where the value
  // defaults to TRUE - as $padPrm [$pad] [name], evaluating the right-hand side.
  //
  // Options that have a handler in an _options/ directory are also queued in
  // $padOptionsAppStart [$pad] for options/_go/app.php to run once the level is set up.

  $padOptionCheck = padOptionCheck ( $padPrmName );

  if ( $padOptionCheck ) {
    $padOptionsAppStart     [$pad] []            = $padPrmName;
    $padOptionsAppStartCall [$pad] [$padPrmName] = $padOptionCheck;
  }

  $padPrm [$pad] [$padPrmName] = ( $padPrmValue === '' ) ? TRUE : padEval ( $padPrmValue );

  $padParmsSetType  = 'option';
  $padParmsSetName  = $padPrmName;
  $padParmsSetValue = $padPrm [$pad] [$padPrmName];

?>