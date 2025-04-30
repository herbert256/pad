<?php

  if ( $pqActionParm === TRUE )
    $pqActionParm = '';

  $pqActionList = padExplode ( $pqActionParm, '|' );

  if ( $pqAction == 'action' ) 
    $pqAction = array_shift ( $pqActionList );

  if ( ! pqAction ( $pqAction ) )
    return;

  include 'sequence/actions/merge.php'; 

  $pqActionParm  = $pqActionList [0] ?? '';
  $pqActionCnt   = ( ctype_digit ( $pqActionParm ) ) ? $pqActionParm : 1;
  $pqActionStart = $pqResult;
  $pqActionKey   = array_key_first ( $pqResult);

  $pqResult = include "sequence/actions/types/$pqAction.php";

  if ( $pqNegative )
    include "sequence/actions/negative/negative.php";

  $pqActionsHit [$pqAction] = array_values ( $pqResult ); 

  include 'sequence/actions/info.php';

?>