<?php

  if ( $pqInc <> 1 )
    $pqRandomlyRand = $pqRandomlyStart + rand ( 0, $pqRandomlySteps ) * $pqInc;
  else
    $pqRandomlyRand = rand ( $pqRandomlyStart, $pqRandomlyEnd ) ;

  if ( pqStore ( $pqBuild ) )
    return $pqFixed [$pqRandomlyRand];
  else
    return $pqRandomlyRand;

?>