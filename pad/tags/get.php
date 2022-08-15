<?php

  $padGetApp     = $padPrmsVal [$pad] [0] ?? padTagParm ('include');
  $padGetPage    = $padPrmsVal [$pad] [1] ?? padTagParm ('page');
  $padGetQuery   = $padPrmsVal [$pad] [2] ?? padTagParm ('parms');
  $padGetInclude = $padPrmsVal [$pad] [3] ?? padTagParm ('include');

  return pad ( $padGetApp, $padGetPage, $padGetQuery, $padGetInclude ) 
 
?>