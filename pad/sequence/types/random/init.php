<?php

  $pqRandomStart = ( $pqMin == PHP_INT_MIN ) ? 1 : $pqMin;
  $pqRandomEnd   = $pqMax;

  if ( $pqInc <> 1 )
    $pqRandomSteps = intval ( ( $pqRandomEnd - $pqRandomStart ) / $pqInc );

?>