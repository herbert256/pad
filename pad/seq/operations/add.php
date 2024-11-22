<?php

  if ( $padSeqSeq == 'operation' ) {
    $padExplode  = explode ('|', $padPrmValue, 2); 
    $padSeqSeq   = $padExplode [0] ?? '';
    $padPrmValue = $padExplode [1] ?? '';
  }

  if ( $padPrmValue and isset ( $padSeqStore [$padPrmValue] ) 
    and file_exists ( "seq/types/$padSeqSeq/flags/operationDouble") ) 

    include 'seq/operations/initsDouble.php';

  elseif ( file_exists ( "seq/types/$padSeqSeq/flags/operationSingle") )

    include 'seq/operations/initsSingle.php';

  else

    return;
    
  $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqType );

  include 'seq/build/include.php';

  $padSeqOperations [] = [
    'padSeqSeq'   => $padSeqSeq,
    'padSeqParm'  => $padPrmValue,
    'padSeqBuild' => $padSeqBuild,
    'padSeqType'  => $padSeqType
  ];

?>