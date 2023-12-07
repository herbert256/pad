<?php

  foreach ( $padPrm [$pad] as $padK => $padV )
    padXref ( 'parms', 'options', $padK );
  
  foreach ( $padSetLvl [$pad] as $padK => $padV )
    padXref ( 'parms', 'lvl', $padK );
  
  foreach ( $padSetOcc [$pad] as $padK => $padV )
    padXref ( 'parms', 'occ', $padK );

?>