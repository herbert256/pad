<?php

  if ( ! $padTrace ) 
    return;

  $padTraceData = [
    'seq'    => $padSeq_seq,
    'set'    => $padSeq_set,
    'parm'   => $padSeq_parm,
    'name'   => $padSeq_name,
    'result' => $padSeqReturn
  ];

  padFilePutContents ( $padLevelDir [$pad] . "/sequence.json", $padTraceData );


?>