<?php

  $padGet_app     = $padPrmsVal [$pad] [0] ?? padTagParm ('include');
  $padGet_page    = $padPrmsVal [$pad] [1] ?? padTagParm ('page');
  $padGet_query   = $padPrmsVal [$pad] [2] ?? padTagParm ('parms');
  $padGet_include = $padPrmsVal [$pad] [3] ?? padTagParm ('include');

  return pad ( $padGet_app, $padGet_page, $padGet_query, $padGet_include ) 
 
?>