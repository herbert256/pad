<?php

  foreach ( $GLOBALS as $padSolK => $padSolV ) 
    if ( padValidStore ( $padSolK ) ) 
      unset ( $GLOBALS [$padSolK] );

  foreach ( $padSolApp [$padSolCnt] as $padSolK => $padSolV ) 
    $GLOBALS [$padSolK] = $padSolV;

?>