<?php

  // Shared loader for the file pair behind a tag; the caller sets $padTagGo to the path without
  // extension. Used by types/app.php, types/common.php and types/pad.php.
  //
  // The .php half runs first through call/ob.php: whatever it prints, followed by the text of
  // the .pad half, becomes $padTagContent - the template text the level goes on to process -
  // while the .php file's return value is returned as the tag's own value.

  $padCall = "$padTagGo.php";
  include PAD . 'call/ob.php';

  $padTagContent = $padCallOB . padFileGet ("$padTagGo.pad");

  return $padCallPHP;

?>