<?php

  if     ( pTag_parm ('app') ) $pExists = APP  . $pParm;
  elseif ( pTag_parm ('pad') ) $pExists = PAD . $pParm;
  else                            $pExists = APP  . $pParm;

  return ( file_exists ($pExists) ) ? TRUE : FALSE;

?>