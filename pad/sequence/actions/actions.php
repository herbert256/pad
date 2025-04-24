<?php

  $pqActionStart = $pqResult;
  $pqResult      = [];

  foreach ( $pqActionStart as $padK => $padV )
    $pqResult [ 'pq_' . $padK ] = $padV;

  if ( $pqAction ) 
    $pqActions [$pqAction] = $pqActionParm;

  foreach ( $padParms [$pad] as $padParmsOne ) {

    extract ( $padParmsOne );

    if  ( $padPrmKind == 'option' )
      $pqActions [$padPrmName] = $padPrmValue;
        
  }
 
  foreach ( $pqActions as $padPrmName => $padPrmValue )
    include 'sequence/actions/action.php';

  $pqResult = array_values ( $pqResult );

?>