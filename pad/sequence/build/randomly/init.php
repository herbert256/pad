<?php

  if ( ! $pqRandomly )
    return;
  
  $pqRandomlyStart = $pqStart;
  $pqRandomlyEnd   = $pqEnd;

  if ( pqStore ( $pqBuild ) )
    if ( $pqRandomlyEnd > count ( $pqFixed ) - 1 )
      $pqRandomlyEnd = count ( $pqFixed ) - 1;

  if ( $pqInc <> 1 )
    $pqRandomlySteps = intval ( ( $pqRandomlyEnd - $pqRandomlyStart ) / $pqInc );
  
?>