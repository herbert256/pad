<?php

  if     ( padTagParm ('app') ) $padExists = APP . $padOpt [$pad] [1];
  elseif ( padTagParm ('pad') ) $padExists = PAD . $padOpt [$pad] [1];
  else                          $padExists = APP . $padOpt [$pad] [1];

  return ( file_exists ($padExists) ) ? TRUE : FALSE;

?>