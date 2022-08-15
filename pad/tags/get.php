<?php

  $padGet_app     = $padPrmsVal [$pad] [0] ?? pTag_parm ('include');
  $padGet_page    = $padPrmsVal [$pad] [1] ?? pTag_parm ('page');
  $padGet_query   = $padPrmsVal [$pad] [2] ?? pTag_parm ('parms');
  $padGet_include = $padPrmsVal [$pad] [3] ?? pTag_parm ('include');

  return pad ( $padGet_app, $padGet_page, $padGet_query, $padGet_include ) 
 
?>