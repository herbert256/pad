<?php

  $padOptCnt++;
  $padOpt [$pad] [$padOptCnt] = padEval ( $padV );

  if ( ! $padFirstParm [$pad] )
    $padFirstParm [$pad] = $padOpt [$pad] [$padOptCnt];
    
?>