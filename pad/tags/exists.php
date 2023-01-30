<?php

  if     ( padTagParm ('app') ) $padExists = APP  . $padPrm [$pad] [1];
  elseif ( padTagParm ('pad') ) $padExists = PAD . $padPrm [$pad] [1];
  else                            $padExists = APP  . $padPrm [$pad] [1];

  return ( file_exists ($padExists) ) ? TRUE : FALSE;

?>