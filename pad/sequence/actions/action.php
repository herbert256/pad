<?php

  if ( $padPrmValue === TRUE )
    $padPrmValue = '';

  $padSeqActionList = padExplode ( $padPrmValue, '|' );

  if ( $padPrmName == 'action' ) $padSeqAction = array_shift ( $padSeqActionList );
  else                           $padSeqAction = $padPrmName;     

  $padSeqActionParm = $padSeqActionList [0] ?? '';

  if ( ! file_exists ( "sequence/actions/types/$padSeqAction.php" ) )
    return;

  if ( $padSeqNegative )
    include "sequence/actions/negative/inits.php";
  
  $padSeqActionCnt = ( ctype_digit ( $padSeqActionParm ) ) ? $padSeqActionParm : 1;

  $padSeqResult = include "sequence/actions/types/$padSeqAction.php";

  if ( $padSeqNegative )
    include "sequence/actions/negative/exits.php";

  $padSeqResult = array_values ( $padSeqResult );
  
  $padSeqNames [] = $padSeqAction;

  if ( file_exists ( "sequence/actions/single/$padSeqAction" ) )
    $padSeqInfo ['actions/single'] [] = $padSeqAction;
  elseif ( file_exists ( "sequence/actions/double/$padSeqAction" ) )
    $padSeqInfo ['actions/double'] [] = $padSeqAction;

?>