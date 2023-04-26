<?php

  $padGetPage    = $padOpt [$pad] [1] ?? padTagParm ('page');
  $padGetQuery   = $padOpt [$pad] [2] ?? padTagParm ('parms');
  $padGetInclude = $padOpt [$pad] [3] ?? padTagParm ('include');

  return pad ( $padGetPage, $padGetQuery, $padGetInclude );
 
?>