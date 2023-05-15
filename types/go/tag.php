<?php

  $padTagGo .= "_tags/". $padTag [$pad];

  $padTagPhp = '';

  if ( padExists("$padTagGo.html") )
    $padTagContent .= padGetHtml ("$padTagGo.html");

  if ( padExists("$padTagGo.php") ) {

    ob_start();

    $padTagPhp = include "$padTagGo.php";

    if ( $padTagPhp === 1 )
      $padTagPhp = '' ;

    $padTagContent .= ob_get_clean() ;

  }

  if ( is_array($padTagPhp) or is_object($padTagPhp) or is_resource($padTagPhp) )
    return $padTagPhp;

  if ( $padTagPhp !== TRUE and $padTagPhp !== FALSE and $padTagPhp !== NULL ) {
    if ( $padTagPhp or $padTagContent )
      $padTagPhp = $padTagContent . $padTagPhp;
    $padTagContent = '';
  }

  return $padTagPhp;

?>