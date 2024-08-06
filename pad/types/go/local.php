<?php

  $padLocalParts = pathinfo ( $padLocalFile );
  $padLocalName  = padTagParm ( 'name', $padLocalParts ['filename']  ?? '' );
  $padLocalExt   = padTagParm ( 'type', $padLocalParts ['extension'] ?? '' );
  $padLocalBox   = padTagParm ( 'sandbox' );

  if ( $padLocalExt == 'php' ) {

    $padCall      = $padLocalFile;
    $padLocalData = include pad . 'call/any.php';
    $padLocalExt  = '';

  } else

    if ( $padLocalBox )
      $padLocalData = padSandbox ( padFileGetContents ($padLocalFile) ); 
    else
      $padLocalData = padCode ( padFileGetContents ($padLocalFile) ); 

  if ( $padLocalName and ! $GLOBALS ['padName'] [$GLOBALS['pad']] )
    $GLOBALS ['padName'] [$GLOBALS['pad']] = $padLocalName;

  return padData ( $padLocalData, $padLocalExt, $padLocalName );
 
?>