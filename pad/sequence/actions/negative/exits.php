<?php

  foreach ( $pqResult as $padK => $padV )

    if ( substr ( $padK, 0, 1 ) <> 'x' ) { 

      $pqNegativeNew = $pqResult;

      $pqResult = [];

      foreach ( $pqNegativeOld as $padV )
        if ( ! in_array ( $padV, $pqNegativeNew ) )
          $pqResult [] = $padV;

      return;

    }

  $pqNegativeKeysNew = array_keys ( $pqResult      );
  $pqNegativeKeysOld = array_keys ( $pqNegativeOld );

  $pqResult = [];

  foreach ( $pqNegativeKeysOld as $pqNegativeOldKey )
    if ( ! in_array ( $pqNegativeOldKey, $pqNegativeKeysNew ) )
      $pqResult [ substr ( $pqNegativeOldKey, 1 ) ] = $pqNegativeOld [$pqNegativeOldKey];
  
?>