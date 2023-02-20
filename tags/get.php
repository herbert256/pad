<?php

  $padGetApp     = $padOpt [$pad] [1] ?? padTagParm ('include');
  $padGetPage    = $padOpt [$pad] [2] ?? padTagParm ('page');
  $padGetQuery   = $padOpt [$pad] [3] ?? padTagParm ('parms');
  $padGetInclude = $padOpt [$pad] [4] ?? padTagParm ('include');

  return pad ( $padGetApp, $padGetPage, $padGetQuery, $padGetInclude );
 
?>