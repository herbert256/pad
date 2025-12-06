<?php

  $padContentPad = "$padcontentCall.pad";
  $padContentPhp = "$padcontentCall.php";

  $padContentData = '';

  if ( file_exists ( $padContentPhp ) ) {
    $padCall = $padContentPhp;
    $padContentData .= include PAD . 'call/obNoOne.php';
  }

  if ( file_exists ( $padContentPad ) ) 
    $padContentData .= padFileGet ( $padContentPad );

  return $padContentData;

?>