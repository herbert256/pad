<?php

  if     ( pTag_parm ('app') ) $pExists = APP  . $pPrm [$p];
  elseif ( pTag_parm ('pad') ) $pExists = PAD . $pPrm [$p];
  else                            $pExists = APP  . $pPrm [$p];

  return ( file_exists ($pExists) ) ? TRUE : FALSE;

?>