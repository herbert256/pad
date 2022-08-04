<?php

  $pad_get_app     = $pad_prms_val [0] ?? pad_tag_parm ('include');
  $pad_get_page    = $pad_prms_val [1] ?? pad_tag_parm ('page');
  $pad_get_query   = $pad_prms_val [2] ?? pad_tag_parm ('parms');
  $pad_get_include = $pad_prms_val [3] ?? pad_tag_parm ('include');

  return pad ( $pad_get_app, $pad_get_page, $pad_get_query, $pad_get_include ) 
 
?>