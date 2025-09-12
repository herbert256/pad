<?php

  $pqRandomlyRand = $pqRandomlyStart + rand ( 0, $pqRandomlySteps ) * $pqInc;

  if ( pqStore ( $pqBuild ) )
    return $pqFixed [$pqRandomlyRand];
  else
    return $pqRandomlyRand;

?>