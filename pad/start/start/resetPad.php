<?php

  for ( $padStrIdx = 0; $padStrIdx < $pad ; $padStrIdx++ ) 
    foreach ( padStrDat as $padStrVal )
      $GLOBALS [$padStrVal] [$padStrIdx] = [];

  foreach ( padStrSto as $padStrVal )
    if ( isset ( $GLOBALS [$padStrVal] ))   
      $GLOBALS [$padStrVal] = [];
   
  unset ( $padSqlConnect );

?>