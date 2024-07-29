<?php

  global $padCodeSave, $padCodeCnt;

  if ( ! isset ( $padCodeCnt ) ) 
    $padCodeCnt = 0;
  else
    $padCodeCnt++;

  $padCodeSave [$padCodeCnt] = padSave ();

?>