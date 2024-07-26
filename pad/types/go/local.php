<?php

  $padLocalParts = pathinfo ( $padLocalFile );
  $padLocalName  = padTagParm ( 'name', $padLocalParts ['filename']  ?? '' );
  $padLocalExt   = padTagParm ( 'type', $padLocalParts ['extension'] ?? '' );

  if ( $padLocalExt == 'php' ) {

    $padCall      = $padLocalFile;
    $padLocalData = include pad . 'call/any.php';
    $padLocalExt  = '';

  } else

    $padLocalData = padFunction ( padFileGetContents ($padLocalFile) ); 

  if ( $padLocalName and ! $GLOBALS ['padName'] [$GLOBALS['pad']] )
    $GLOBALS ['padName'] [$GLOBALS['pad']] = $padLocalName;

  return padData ( $padLocalData, $padLocalExt, $padLocalName );
 
?>