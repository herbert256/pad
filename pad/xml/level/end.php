<?php

  if ( $padXmlOcc [$pad] == 'done' )
    padXmlWriteClose ( 'occurs' );

  if ( $pad > 0 )
    padXmlWriteClose ( $padXmlTag [$pad] );

  $padXmlTag [$pad] = '';
  $padXmlOcc [$pad] = '';

  $padXmlOb        = '';
  $padXmlTagReturn = '';

?>