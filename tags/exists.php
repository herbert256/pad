<?php

  if     ( pad_tag_parm ('app') ) $pad_exists = PAD_APP  . $pad_parm;
  elseif ( pad_tag_parm ('pad') ) $pad_exists = PAD_HOME . $pad_parm;
  else                            $pad_exists = PAD_APP  . $pad_parm;

  return ( file_exists ($pad_exists) ) ? TRUE : FALSE;

?>