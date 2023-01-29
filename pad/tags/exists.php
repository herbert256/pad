<?php

  if     ( padTagParm ('app') ) $padExists = APP  . $padPrm [$pad] [0];
  elseif ( padTagParm ('pad') ) $padExists = PAD . $padPrm [$pad] [0];
  else                            $padExists = APP  . $padPrm [$pad] [0];

  return ( file_exists ($padExists) ) ? TRUE : FALSE;

?>