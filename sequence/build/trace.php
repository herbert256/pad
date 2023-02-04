<?php

  if ( ! $padTrace ) 
    return;

  $padTraceData = [
    'seq'    => $padSeqSeq,
    'set'    => $padSeqSet,
    'parm'   => $padSeqParm,
    'name'   => $padSeqName,
    'result' => $padSeqReturn
  ];

  padFilePutContents ( $padLevelDir [$pad] . "/sequence.json", $padTraceData );


?>