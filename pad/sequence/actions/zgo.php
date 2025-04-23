<?php

  foreach ( $pqActions as $pqAction => $pqActionParm ) {

    if ( $pqActionParm === TRUE )
      $pqActionParm = '';

    $pqActionList = padExplode ( $pqActionParm, '|' );
    $pqActionParm = $pqActionList [0] ?? '';
    $pqActionCnt  = ( ctype_digit ( $pqActionParm ) ) ? $pqActionParm : 1;
    $pqNames []   = $pqAction;

    if ( $pqNegative )
      include "sequence/actions/negative/inits.php";
    
    $pqResult = include "sequence/actions/types/$pqAction.php";

    if ( $pqNegative )
      include "sequence/actions/negative/exits.php";

    include 'sequence/actions/info.php';

  }

?>