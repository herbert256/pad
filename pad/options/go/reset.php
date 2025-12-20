<?php

  $padGetName = padTagParm ( $padReset );
  $padContent = include PAD . 'get/content.php';

  $padTagResult  = TRUE;
  $padTagContent = '';

  $padNull  [$pad] = FALSE;
  $padElse  [$pad] = FALSE;
  $padHit   [$pad] = TRUE;
  $padArray [$pad] = FALSE;

  $padData  [$pad] = padDefaultData ();

  if ( isset ( $padPrm [$pad] ['content']) )
    unset ( $padPrm [$pad] ['content'] );

  return TRUE;

?>