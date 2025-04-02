<?php

  foreach ( $padSeqResult as $padK => $padV )

    if ( substr ( $padK, 0, 1 ) <> 'x' ) { 

      $padSeqNegativeNew = $padSeqResult;

      $padSeqResult = [];

      foreach ( $padSeqNegativeOld as $padV )
        if ( ! in_array ( $padV, $padSeqNegativeNew ) )
          $padSeqResult [] = $padV;

      return;

    }

  $padSeqNegativeKeysNew = array_keys ( $padSeqResult      );
  $padSeqNegativeKeysOld = array_keys ( $padSeqNegativeOld );

  $padSeqResult = [];

  foreach ( $padSeqNegativeKeysOld as $padSeqNegativeOldKey )
    if ( ! in_array ( $padSeqNegativeOldKey, $padSeqNegativeKeysNew ) )
      $padSeqResult [ substr ( $padSeqNegativeOldKey, 1 ) ] = $padSeqNegativeOld [$padSeqNegativeOldKey];
  
?>