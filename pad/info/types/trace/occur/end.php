<?php

  // Traces the end of one occurrence, from events/occurEnd.php.
  //
  // First lets events/resultOcc.php report the output this pass produced, then writes the
  // 'occur end' line, deletes duplicate trace files in the occurrence's directory, stamps its
  // child count onto the directory name and resets that counter for the next pass.

  include PAD . 'events/resultOcc.php';

  if ( ! isset ( $padInfoTraceLevel [$pad] ) ) padInfoTraceSet ( $pad );
  if ( ! $padInfoTraceLevel [$pad]           ) padInfoTraceSet ( $pad );

  $padI = $padOccur [$pad] ?? 0;

  if ( ! isset ($padInfoTraceOccurChilds [$pad]         ) ) $padInfoTraceOccurChilds [$pad] [$padI] = 0;
  if ( ! isset ($padInfoTraceOccurChilds [$pad] [$padI] ) ) $padInfoTraceOccurChilds [$pad] [$padI] = 0;

  if ( $padInfoTraceStartEndOcc )
   padInfoTrace ( 'occur', 'end', $padOut [$pad] );

  if ( $padInfoTraceLocalChk )
    padInfoTraceCheckLocal ( $padInfoTraceLevel [$pad] . "/$padI" );

  if ( $padInfoTraceChilds )
    padInfoTraceChilds ( $padInfoTraceLevel [$pad] . "/$padI", $padInfoTraceOccurChilds [$pad] [$padI], 'occur' );

  $padInfoTraceOccurChilds [$pad] [$padI] = 0;

?>