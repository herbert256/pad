<?php

  if ( ! count ( $pqActions ) )
    return;

  include PQ . 'actions/assoc.php';

  foreach ( $pqActions as $pqAction => $pqActionParm ) {

    $pqActionStart = $pqResult;
    $pqActionList  = padExplode ( $pqActionParm, '|' );
    $pqActionParm  = $pqActionList [0] ?? '';
    $pqActionCnt   = ( ctype_digit ( $pqActionParm ) ) ? $pqActionParm : 1;
    $pqActionKey   = array_key_first ( $pqResult);

    if ( str_contains ( $pqActionParm, '..' ) )
      pqRandomParm ( $pqActionParm );

    include PQ . 'actions/merge.php'; 
    include PQ . "actions/types/$pqAction.php";
    include PQ . 'actions/negative/negative.php';

    $pqActionsHit [$pqAction] = array_values ( $pqResult ); 

  }

?>