<?php

  if     ( padTagParm ('app') ) $padExists = APP  . $padPrm [$pad];
  elseif ( padTagParm ('pad') ) $padExists = PAD . $padPrm [$pad];
  else                            $padExists = APP  . $padPrm [$pad];

  return ( file_exists ($padExists) ) ? TRUE : FALSE;

?>