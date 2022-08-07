<?php

  if     ( pTag_parm ('app') ) $pExists = APP  . $pParm[$p];
  elseif ( pTag_parm ('pad') ) $pExists = PAD . $pParm[$p];
  else                            $pExists = APP  . $pParm[$p];

  return ( file_exists ($pExists) ) ? TRUE : FALSE;

?>