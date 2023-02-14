<?php

  $padGetApp     = $padPrm [$pad] [1] ?? padTagParm ('include');
  $padGetPage    = $padPrm [$pad] [2] ?? padTagParm ('page');
  $padGetQuery   = $padPrm [$pad] [3] ?? padTagParm ('parms');
  $padGetInclude = $padPrm [$pad] [4] ?? padTagParm ('include');

  return pad ( $padGetApp, $padGetPage, $padGetQuery, $padGetInclude );
 
?>