<?php

  if ( $padPrmValue and isset ( $padSeqStore [$padPrmValue] ) 
    and file_exists ( "sequence/types/$padSeqSeq/flags/playDouble") ) 

    include 'sequence/plays/initsDouble.php';

  elseif ( file_exists ( "sequence/types/$padSeqSeq/flags/playSingle") )

    include 'sequence/plays/initsSingle.php';

  else

    return;
    
  $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqPlay );

  include 'sequence/build/include.php';

  $padSeqPlays [] = [
    'padSeqSeq'   => $padSeqSeq,
    'padSeqParm'  => $padPrmValue,
    'padSeqBuild' => $padSeqBuild,
    'padSeqPlay'  => $padSeqPlay
  ];

  $padSeqDone [] = $padSeqSeq;

?>