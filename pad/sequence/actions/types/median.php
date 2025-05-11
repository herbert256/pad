<?php

  $pqTmp    = count ( $pqResult ) / 2;
  $pqMedian = (int) $pqTmp;

  $pqI = 0;

  foreach ( $pqActionStart as $padK => $padV ) {
    if ( $pqI == $pqMedian )
       $pqResult = [ $padK => $padV ];
    $pqI++;
  }
  
?>