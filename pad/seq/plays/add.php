<?php

  if ( $padSeqSeq == 'play' ) {
    $padExplode  = explode ('|', $padPrmValue, 2); 
    $padSeqSeq   = $padExplode [0] ?? '';
    $padPrmValue = $padExplode [1] ?? '';
  }

  if ( $padPrmValue and isset ( $padSeqStore [$padPrmValue] ) 
    and file_exists ( "seq/types/$padSeqSeq/flags/playDouble") ) 

    include 'seq/plays/initsDouble.php';

  elseif ( file_exists ( "seq/types/$padSeqSeq/flags/playSingle") )

    include 'seq/plays/initsSingle.php';

  else

    return;
    
  $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqType );

  include 'seq/build/include.php';

  $padSeqPlays [] = [
    'padSeqSeq'   => $padSeqSeq,
    'padSeqParm'  => $padPrmValue,
    'padSeqBuild' => $padSeqBuild,
    'padSeqType'  => $padSeqType
  ];

  unset ( $padSeqOptions [$padSeqOptionKey] );

?>