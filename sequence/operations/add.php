<?php

  if ( $padPrmValue and isset ( $padSeqStore [$padPrmValue] ) 
    and file_exists ( "/pad/sequence/types/$padSeqSeq/flags/operationDouble") ) 

    include '/pad/sequence/operations/initsDouble.php';

  elseif ( file_exists ( "/pad/sequence/types/$padSeqSeq/flags/operationSingle") )

    include '/pad/sequence/operations/initsSingle.php';

  else

    return;
    
  $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqType );

  include '/pad/sequence/build/include.php';

  $padSeqOperations [] = [
    'padSeqSeq'   => $padSeqSeq,
    'padSeqParm'  => $padPrmValue,
    'padSeqBuild' => $padSeqBuild,
    'padSeqType'  => $padSeqType
  ];

?>