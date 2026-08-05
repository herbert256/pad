<?php

  // Loads one page/include pair and returns it as still unprocessed template text: what
  // $padGetCall.php echoes, followed by the contents of $padGetCall.pad.
  //
  // $padGetCall is a path without extension; either half may be missing. Only the echoed
  // output of the .php is used - its return value is not part of the result.

  $padGetPad = "$padGetCall.pad";
  $padGetPhp = "$padGetCall.php";

  $padGetData = '';

  if ( file_exists ( $padGetPhp ) ) {
    $padCall = $padGetPhp;
    $padGetData .= include PAD . 'call/obNoOne.php';
  }

  if ( file_exists ( $padGetPad ) )
    $padGetData .= padFileGet ( $padGetPad );

  return $padGetData;

?>