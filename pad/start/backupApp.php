<?php

  $padSolApp [$padSolCnt] = [];

  foreach ( $GLOBALS as $padSolK => $padSolV )
    if ( padValidStore ($padSolK) )
      $padSolApp [$padSolCnt] [$padSolK] = $GLOBALS [$padSolK];
  
?>