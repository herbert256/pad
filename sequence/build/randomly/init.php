<?php

  if ( ! $pqRandomly )
    return;
  
  $pqRandomlyStart = $pqFrom;
  $pqRandomlyEnd   = $pqTo;

  if ( pqStore ( $pqBuild ) )
    if ( $pqRandomlyEnd > count ( $pqFixed ) - 1 )
      $pqRandomlyEnd = count ( $pqFixed ) - 1;

  $pqRandomlySteps = intval ( ( $pqRandomlyEnd - $pqRandomlyStart ) / $pqInc );
  
?>