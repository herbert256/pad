<?php

  $padStoreName = $padPrm [$pad] ['toBool'];

  if     ( $padNull [$pad]  ) $padBoolStore [$padStoreName] = FALSE;
  elseif ( $padElse [$pad]  ) $padBoolStore [$padStoreName] = FALSE;
  elseif ( trim ( $padResult [$pad] ) <> '' ) $padBoolStore [$padStoreName] = TRUE;
  else                                             $padBoolStore [$padStoreName] = FALSE;

  $padResult [$pad] = '';
  
?>