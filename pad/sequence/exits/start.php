<?php

  // Earlier single-file version of the debug info block, now split over exits/info.php and
  // exits/info/{start,actions,options,plays}.php. Nothing includes it any more, and it has
  // drifted from the live copies (it explodes $pqStartParts, which is never set, and keys
  // $pqInfo as 'start<x>' rather than 'start/<x>'). Kept for reference only.

  if ( ! $padInfo )
    return;

  foreach ( $pqActions as $pqAction => $pqActionParm )
    if ( file_exists ( PQ . "actions/single/$pqAction" ) )
      $pqInfo ['actions/single'] [] = $pqAction;
    elseif ( file_exists ( PQ . "actions/double/$pqAction" ) )
      $pqInfo ['actions/double'] [] = $pqAction;

  foreach ( $pqPlays as $pqTmp ) {
    extract ( $pqTmp );
    $pqInfo ['plays'] [] = "$pqPlay/$pqSeq";
  }

  foreach ( $padParms [$pad] as $padStartOption ) {
    extract ( $padStartOption );
    if ( $padPrmKind == 'option' )
      if ( file_exists ( PQ . "options/types/$padPrmName.php") )
        $pqInfo ['options'] [] = $padPrmName;
  }

  if ( ! isset ( $pqSetStart) )
    $pqSetStart = 'unknown/type';

  $pqSetStart = str_replace ( PQ ,      '', $pqSetStart );
  $pqSetStart = str_replace ( 'start/', '', $pqSetStart );
  $pqSetStart = str_replace ( '.php',   '', $pqSetStart );

  $pqStartP = padExplode ( $pqStartParts, '/' );
  $pqStart1 = $pqStartP [0] ?? '';
  $pqStart2 = $pqStartP [1] ?? '';

  $pqInfo ['start'.$pqStart1] [] = $pqStart2;

  unset ($pqSetStart);

  include PAD . 'events/sequence.php';

?>