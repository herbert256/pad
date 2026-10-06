<?php

  $padCommon = FALSE;

  $padOutputType = 'nosuchtype';

  // The level page: an error level that does not exist, under an output type that does and
  // an error action that reads the level - the boot action a tool's request gets reads none.

  if ( $padPage == 'level' ) {
    $padOutputType  = 'web';
    $padErrorAction = 'pad';
    $padErrorLevel  = 'warnings';
  }

?>
