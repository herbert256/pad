<?php

  include 'sequence/actions/assoc.php';

  if ( $pqAction ) 
    $pqActions [$pqAction] = $pqActionParm;

  foreach ( $padParms [$pad] as $padV )
    if ( $padV ['padPrmKind'] == 'option' )
      $pqActions [ $padV ['padPrmName'] ] = $padV ['padPrmValue'];
 
  foreach ( $pqActions as $pqAction => $pqActionParm )
    include 'sequence/actions/action.php';

?>