<?php

  // Shared loader for the file pair behind a tag; the caller sets $padTagGo to the path without
  // extension. Used by types/app.php, types/common.php and types/pad.php.
  //
  // The .php half runs first through call/ob.php: whatever it prints, followed by the text of
  // the .pad half, becomes $padTagContent - the template text the level goes on to process -
  // while the .php file's return value is returned as the tag's own value.

  // What the application's variables were before the tag's PHP ran: in a page run the
  // level loop is at global scope, so the tag's variables are globals too, and the filter
  // below - anything not already a global - dropped every one of them.

  $padTagBefore = [];

  foreach ( $GLOBALS as $padK => $padV )
    if ( padValidStore ( $padK ) )
      $padTagBefore [$padK] = $padV;

  $padCall = "$padTagGo.php";
  include PAD . 'call/ob.php';

  // The variables the tag's PHP half added or changed are published to $padLvlFunVar,
  // where the function group of the @ subsystem reads them - the store level/function.php
  // fills for a template driven by a PHP function, and the callbacks fill per phase. The
  // storable-name filter keeps the engine's own pad-prefixed names out.

  foreach ( get_defined_vars () as $padK => $padV )
    if ( padValidStore ( $padK )
         and ( ! array_key_exists ( $padK, $padTagBefore ) or $padTagBefore [$padK] !== $padV ) )
      $GLOBALS ['padLvlFunVar'] [ $GLOBALS ['pad'] ] [$padK] = $padV;

  unset ( $padTagBefore );

  $padTagContent = $padCallOB . padFileGet ("$padTagGo.pad");

  return $padCallPHP;

?>