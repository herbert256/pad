<?php

  if ( $padPrmValue === TRUE )
    $padPrmValue = '';

  $pqActionList = padExplode ( $padPrmValue, '|' );

  if ( $padPrmName == 'action' ) $pqAction = array_shift ( $pqActionList );
  else                           $pqAction = $padPrmName;     

  if ( ! pqAction ( $pqAction ) )
    return;

  $pqActionParm = $pqActionList [0] ?? '';
  $pqActionCnt  = ( ctype_digit ( $pqActionParm ) ) ? $pqActionParm : 1;

  if ( $pqNegative )
    include "sequence/actions/negative/inits.php";
  
  $pqResult = include "sequence/actions/types/$pqAction.php";

  if ( $pqNegative )
    include "sequence/actions/negative/exits.php";

  $pqNames [] = $pqAction;

  include 'sequence/actions/info.php';

?>