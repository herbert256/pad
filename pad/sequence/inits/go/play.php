<?php 

  if ( isset ( $padSeqStore[$padSeqFirst] ) and file_exists ( "sequence/types/$padSeqSecond" ) ) {
    $padSeqPull = $padSeqFirst;
    $padSeqSeq  = $padSeqSecond;
  } elseif ( isset ( $padSeqStore[$padSeqSecond] ) and file_exists ( "sequence/types/$padSeqFirst" ) ) {
    $padSeqPull = $padSeqSecond;
    $padSeqSeq  = $padSeqFirst;
  } 

  if ( is_numeric ($padSeqParm) )
    if ( str_contains ( $padSeqParm, '.' ) )  $padSeqParm = doubleval ( $padSeqParm );
    else                                      $padSeqParm = intval ( $padSeqParm );

  $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqPlay );

  include 'sequence/build/include.php';

  $padSeqFixed = [];

  $padSeqInfo ["start/$padStartType/play/$padSeqPlay"] [] = $padSeqSeq;

  foreach ( $padSeqStore [$padSeqPull] as $padSeqLoop ) {

    $padSeq = include 'sequence/build/call.php';
  
    if     ( $padSeqPlay == 'make'   and $padSeq === TRUE       ) continue;
    elseif ( $padSeqPlay == 'make'   and $padSeq === FALSE      ) continue;
    elseif ( $padSeqPlay == 'make'   and $padSeq == $padSeqLoop ) $padSeqFixed [] = $padSeq;
    elseif ( $padSeqPlay == 'make'   and $padSeq <> $padSeqLoop ) $padSeqFixed [] = $padSeq;

    elseif ( $padSeqPlay == 'keep'   and $padSeq === TRUE       ) $padSeqFixed [] = $padSeqLoop;
    elseif ( $padSeqPlay == 'keep'   and $padSeq === FALSE      ) continue;
    elseif ( $padSeqPlay == 'keep'   and $padSeq == $padSeqLoop ) $padSeqFixed [] = $padSeq;
    elseif ( $padSeqPlay == 'keep'   and $padSeq <> $padSeqLoop ) continue;

    elseif ( $padSeqPlay == 'remove' and $padSeq === TRUE       ) continue;
    elseif ( $padSeqPlay == 'remove' and $padSeq === FALSE      ) $padSeqFixed [] = $padSeqLoop;
    elseif ( $padSeqPlay == 'remove' and $padSeq == $padSeqLoop ) continue;
    elseif ( $padSeqPlay == 'remove' and $padSeq <> $padSeqLoop ) $padSeqFixed [] = $padSeq;

  }

  include "sequence/inits/go/start.php";

?>