<?php

  $padTagGo .= "_tags/". $padTag [$pad];

  $padTagContent = padFileGetContents ("$padTagGo.html");

  $padCall   = "$padTagGo.php";
  $padTagPhp = include pad . 'call/any.php';

  if ( is_array($padTagPhp) or is_object($padTagPhp) or is_resource($padTagPhp) )
    return $padTagPhp;

  if ( $padTagPhp !== TRUE and $padTagPhp !== FALSE and $padTagPhp !== NULL ) {
    if ( $padTagPhp or $padTagContent )
      $padTagPhp = $padTagContent . $padTagPhp;
    $padTagContent = '';
  }

  return $padTagPhp;

?>