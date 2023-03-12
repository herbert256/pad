<?php

  $padTable [$pad] = [];

  for ( $padK = 1; $padK <= $pad; $padK++ )
    if ( $padTableTag [$padK] )
      $padTable [$pad] [$padTableTag[$padK]] = $padCurrent [$padK] ;

  #padTableGetInfo ();
  #padTableGetMain ();
  
  foreach ( $padTable [$pad] as $padK => $padV)
    if (  ! isset($GLOBALS [$padK] ) )
      padSetGlobal ( $padK, $padV );

  foreach ( $padTable [$pad] as $padK => $padV)
    foreach ( $padV as $padK2 => $padV2)
      if (  ! isset($GLOBALS [$padK2] ) )
        padSetGlobal ( $padK2, $padV2 );

?>