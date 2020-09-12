<?php

  if     ( pad_tag_flag ('app') ) $pad_exists = PAD_APP  . $pad_parm;
  elseif ( pad_tag_flag ('pad') ) $pad_exists = PAD_HOME . $pad_parm;
  else                            $pad_exists = PAD_APP  . $pad_parm;

  return ( file_exists ($pad_exists) ) ? TRUE : FALSE;

?>