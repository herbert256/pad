<?php

  if ( $pqAction ) {
    $padPrmName  = $pqAction;
    $padPrmValue = $pqActionParm;
    include 'sequence/actions/action.php';
  }

  foreach ( $padParms [$pad] as $padStartOption ) {

    extract ( $padStartOption );

    if     ( $padPrmKind <> 'option'         ) continue;
    elseif ( pqDone ( $padPrmName, $pqDone ) ) continue;
    elseif ( $padPrmName == 'action'         ) include 'sequence/actions/action.php';
    elseif ( pqAction ( $padPrmName )        ) include 'sequence/actions/action.php';
    
  }
 
?>