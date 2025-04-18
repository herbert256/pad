<?php

  if ( $padPrmValue === TRUE )
    $padPrmValue = '';

  $pqActionList = padExplode ( $padPrmValue, '|' );

  if ( $padPrmName == 'action' ) $pqAction = array_shift ( $pqActionList );
  else                           $pqAction = $padPrmName;     

  $pqActionParm = $pqActionList [0] ?? '';

  if ( ! pqAction ( $pqAction ) )
    return;

  if ( $pqNegative )
    include "sequence/actions/negative/inits.php";
  
  $pqActionCnt = ( ctype_digit ( $pqActionParm ) ) ? $pqActionParm : 1;

  $pqResult = include "sequence/actions/types/$pqAction.php";

  if ( $pqNegative )
    include "sequence/actions/negative/exits.php";

  $pqResult = array_values ( $pqResult );
  
  $pqNames [] = $pqAction;

  if ( file_exists ( "sequence/actions/single/$pqAction" ) )
    $pqInfo ['actions/single'] [] = $pqAction;
  elseif ( file_exists ( "sequence/actions/double/$pqAction" ) )
    $pqInfo ['actions/double'] [] = $pqAction;

?>