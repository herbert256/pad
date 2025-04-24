<?php

  include 'sequence/actions/assoc.php';

  if ( $pqAction ) 
    $pqActions [$pqAction] = $pqActionParm;

  foreach ( $padParms [$pad] as $padParmsOne ) {

    extract ( $padParmsOne );

    if  ( $padPrmKind == 'option' )
      $pqActions [$padPrmName] = $padPrmValue;
        
  }
 
  foreach ( $pqActions as $padPrmName => $padPrmValue )
    include 'sequence/actions/action.php';

?>