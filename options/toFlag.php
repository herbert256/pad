<?php

  $padStoreName = $padPrm [$pad] ['toFlag'];

  if     ( $padNull [$pad]  ) $padFlagStore [$padStoreName] = FALSE;
  elseif ( $padElse [$pad]  ) $padFlagStore [$padStoreName] = FALSE;
  elseif ( trim ( $padResult [$pad] ) <> '' ) $padFlagStore [$padStoreName] = TRUE;
  else                                             $padFlagStore [$padStoreName] = FALSE;

  $padResult [$pad] = '';
  
?>