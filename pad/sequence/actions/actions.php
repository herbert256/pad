<?php

  if ( ! count ( $pqActions ) )
    return;

  include 'sequence/actions/assoc.php';

  foreach ( $pqActions as $pqAction => $pqActionParm ) {

    $pqActionStart = $pqResult;
    $pqActionList  = padExplode ( $pqActionParm, '|' );
    $pqActionParm  = $pqActionList [0] ?? '';
    $pqActionCnt   = ( ctype_digit ( $pqActionParm ) ) ? $pqActionParm : 1;
    $pqActionKey   = array_key_first ( $pqResult);

    if ( str_contains ( $pqActionParm, '..' ) )
      pqRandomParm ( $pqActionParm );

    include 'sequence/actions/merge.php'; 
    include "sequence/actions/types/$pqAction.php";
    include 'sequence/actions/negative/negative.php';

    $pqActionsHit [$pqAction] = array_values ( $pqResult ); 

  }

?>