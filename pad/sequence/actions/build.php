<?php

  if ( $pqAction ) 
    $pqActions [$pqAction] = $pqActionParm;

  foreach ( $padParms [$pad] as $padParmsOne ) {

    extract ( $padParmsOne );

    if     ( $padPrmKind <> 'option'         ) continue;
    elseif ( pqDone ( $padPrmName, $pqDone ) ) continue;

    if ( $padPrmName == 'action' )
      padSplit ( '|', $padPrmValue, $padPrmName, $padPrmValue );
    
    if ( pqAction ( $padPrmName ) )     
      $pqActions [$padPrmName] = $padPrmValue;
    
  }
 
?>