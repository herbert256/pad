<?php

  if ( $padSeqSeq == 'play' ) {
    $padExplode  = explode ('|', $padPrmValue, 2); 
    $padSeqSeq   = $padExplode [0] ?? '';
    $padPrmValue = $padExplode [1] ?? '';
  }

  if ( $padPrmValue and isset ( $padSeqStore [$padPrmValue] ) 
    and file_exists ( "sequence/types/$padSeqSeq/flags/playDouble") ) 

    include 'sequence/plays/initsDouble.php';

  elseif ( file_exists ( "sequence/types/$padSeqSeq/flags/playSingle") )

    include 'sequence/plays/initsSingle.php';

  else

    return;
    
  $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqType );

  include 'sequence/build/include.php';

  $padSeqPlays [] = [
    'padSeqSeq'   => $padSeqSeq,
    'padSeqParm'  => $padPrmValue,
    'padSeqBuild' => $padSeqBuild,
    'padSeqType'  => $padSeqType
  ];

  unset ( $padSeqOptions [$padSeqOptionKey] );

?>