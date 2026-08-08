<?php

  // Shared loader for the file pair behind a tag; the caller sets $padTagGo to the path without
  // extension. Used by types/app.php, types/common.php and types/pad.php.
  //
  // The .php half runs first through call/ob.php: whatever it prints, followed by the text of
  // the .pad half, becomes $padTagContent - the template text the level goes on to process -
  // while the .php file's return value is returned as the tag's own value.

  $padCall = "$padTagGo.php";
  include PAD . 'call/ob.php';

  // The locals the tag's PHP half leaves behind are published to $padLvlFunVar, where the
  // function group of the @ subsystem reads them - the store level/function.php fills for a
  // template driven by a PHP function, and the callbacks fill per phase. The storable-name
  // filter keeps the engine's own pad-prefixed locals out.

  foreach ( get_defined_vars () as $padK => $padV )
    if ( padValidStore ( $padK ) and ! array_key_exists ( $padK, $GLOBALS ) )
      $GLOBALS ['padLvlFunVar'] [ $GLOBALS ['pad'] ] [$padK] = $padV;

  $padTagContent = $padCallOB . padFileGet ("$padTagGo.pad");

  return $padCallPHP;

?>