<?php

  if ( ! file_exists ( "sequence/types/$padSeqSeq" ) )
    return;
    
  $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqPlay );

  include 'sequence/build/include.php';

  if ( file_exists ( "sequence/types/$padSeqSeq/init.php" ) )
    include "sequence/plays/init.php";

  if ( $padPrmValue === TRUE )
    $padPrmValue = '';
  
  $padSeqPlays [] = [
    'padSeqSeq'   => $padSeqSeq,
    'padSeqParm'  => $padPrmValue,
    'padSeqBuild' => $padSeqBuild,
    'padSeqPlay'  => $padSeqPlay
  ];

  if ( in_array ( $padSeqPlay, ['make','keep','remove','flag'] ) ) 
    $padSeqLast = array_key_last ( $padSeqPlays);
  else
    $padSeqLast = FALSE;

  $padSeqDone [] = $padSeqSeq;

?>