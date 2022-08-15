<?php

  $padStore_name = $padPrmsTag [$pad] ['toFlag'];

  if     ( $padNull [$pad]  ) $padFlagStore [$padStore_name] = FALSE;
  elseif ( $padElse [$pad]  ) $padFlagStore [$padStore_name] = FALSE;
  elseif ( trim ( $padResult [$pad] ) <> '' ) $padFlagStore [$padStore_name] = TRUE;
  else                                             $padFlagStore [$padStore_name] = FALSE;

  $padResult [$pad] = '';
  
?>