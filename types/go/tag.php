<?php

  $padTagGo .= "_tags/". $padTag [$pad];

  $padTagContent = padFileGetContents ("$padTagGo.html");

  $padCall   = "$padTagGo.php";
  $padTagPhp = include 'call/any.php';

  if ( is_array($padTagPhp) )
    return $padTagPhp;

  if ( $padTagPhp !== TRUE and $padTagPhp !== FALSE and $padTagPhp !== NULL )
    return $padTagContent . $padTagPhp;

  if ( $padTagPhp === TRUE AND $padTagContent <> '' )
    return $padTagContent;

  return $padTagPhp;

?>