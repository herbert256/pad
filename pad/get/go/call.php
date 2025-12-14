<?php

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