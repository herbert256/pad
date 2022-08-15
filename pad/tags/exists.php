<?php

  if     ( pTag_parm ('app') ) $padExists = APP  . $padPrm [$pad];
  elseif ( pTag_parm ('pad') ) $padExists = PAD . $padPrm [$pad];
  else                            $padExists = APP  . $padPrm [$pad];

  return ( file_exists ($padExists) ) ? TRUE : FALSE;

?>