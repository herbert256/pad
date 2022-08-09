<?php

  if ( ! $pTrace ) 
    return;

  $pTraceData = [
    'seq'    => $pSeq_seq,
    'set'    => $pSeq_set,
    'parm'   => $pSeq_parm,
    'name'   => $pSeq_name,
    'result' => $pSeqReturn
  ];

  pFile_put_contents ( $pLevelDir [$p] . "/sequence.json", $pTraceData );


?>