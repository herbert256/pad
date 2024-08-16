<?php

  for ( $padStrIdx = 0; $padStrIdx < $pad; $padStrIdx++ ) {

    foreach ( padStrDat as $padStrVal ) 
      $GLOBALS [$padStrVal] [$padStrIdx] = $padStrSav [$padStrCnt] [$padStrVal] [$padStrIdx];

    reset ( $padData [$padStrIdx] );

    while ( current ( $padData [$padStrIdx] ) !== false and 
            key ( $padData [$padStrIdx] ) <> $padKey [$padStrIdx] )
      next ( $padData [$padStrIdx] );
  
  }

?>