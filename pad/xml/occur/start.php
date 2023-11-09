<?php

  if ( ! isset ( $padXmlTag [$pad] ) or ! $padXmlTag [$pad] ) 
    include pad . 'xml/level/start.php';

  if ( ! $padXmlOcc [$pad] )
    $padXmlOcc [$pad] = padXmlOccurs ( $pad );
 
  if ( $padXmlOcc [$pad] == 'yes' ) {
    $padXmlOcc [$pad] = 'done';
    padXmlWriteOpen ( 'occurs' );
  }

  if ( $padXmlOcc [$pad] <> 'done' )
    return;

  $padXmlParms = [
    'nr'   => $padOccur     [$pad],
    'type' => $padOccurType [$pad]
  ];

  padXmlWriteOpen ( 'occur', $padXmlParms );

?>