<?php

  $padGetName = padTagParm ( $padReset );
  $padContent = include PAD . 'get/content.php'; 

  $padNull [$pad] =  FALSE;
  $padElse [$pad] =  FALSE;
  $padHit  [$pad] =  TRUE;

  $padTagContent = '';
  
  if ( isset ( $padPrm [$pad] ['content']) )
    unset ( $padPrm [$pad] ['content'] );

  $padData [$pad] = padDefaultData ();

  return TRUE;     

?>