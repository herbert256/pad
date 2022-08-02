<?php

  $pad_app     = $pad_parms_val [0] ?? pad_tag_parm ('include');
  $pad_page    = $pad_parms_val [1] ?? pad_tag_parm ('page');
  $pad_query   = $pad_parms_val [2] ?? pad_tag_parm ('parms');
  $pad_include = $pad_parms_val [3] ?? pad_tag_parm ('include');

  return pad ( $pad_app, $pad_page, $pad_query, $pad_include ) 
 
?>