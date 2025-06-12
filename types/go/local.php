<?php

  $padLocalParts = pathinfo ( $padLocalFile );
  $padLocalName  = padTagParm ( 'name', $padLocalParts ['filename']  ?? '' );
  $padLocalExt   = padTagParm ( 'type', $padLocalParts ['extension'] ?? '' );
  $padLocalBox   = padTagParm ( 'sandbox' );

  if ( $padLocalExt == 'php' ) {

    $padCall      = $padLocalFile;
    $padLocalData = include 'call/any.php';
    $padLocalExt  = '';

  } else

    if ( $padLocalBox )
      $padLocalData = padSandbox ( padFileGet ($padLocalFile) ); 
    else
      $padLocalData = padCode ( padFileGet ($padLocalFile) ); 

  if ( $padLocalName and ! $GLOBALS ['padName'] [$GLOBALS['pad']] )
    $GLOBALS ['padName'] [$GLOBALS['pad']] = $padLocalName;

  return padData ( $padLocalData, $padLocalExt, $padLocalName );

?>