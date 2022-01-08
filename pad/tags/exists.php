<?php

  if     ( pad_tag_parm ('app') ) $pad_exists = APP  . $pad_parm;
  elseif ( pad_tag_parm ('pad') ) $pad_exists = PAD . $pad_parm;
  else                            $pad_exists = APP  . $pad_parm;

  return ( pad_file_exists ($pad_exists) ) ? TRUE : FALSE;

?>