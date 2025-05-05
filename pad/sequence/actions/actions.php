<?php

  if ( ! count ( $pqActions ) )
    return;

  include 'sequence/actions/assoc.php';

  foreach ( $pqActions as $pqAction => $pqActionParm ) {

    $pqActionList = padExplode ( $pqActionParm, '|' );

    include 'sequence/actions/merge.php'; 

    $pqActionParm  = $pqActionList [0] ?? '';
    $pqActionCnt   = ( ctype_digit ( $pqActionParm ) ) ? $pqActionParm : 1;
    $pqActionStart = $pqResult;
    $pqActionKey   = array_key_first ( $pqResult);

    $pqResult = include "sequence/actions/types/$pqAction.php";

    if ( $pqNegative )
      include "sequence/actions/negative/negative.php";

    $pqActionsHit [$pqAction] = array_values ( $pqResult ); 

  }

?>