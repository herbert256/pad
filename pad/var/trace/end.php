<?php

  $pTrace_data = [ 
    'field'   => '{' . $pBetween . '}',
    'nr'      => $pFldCnt,
    'source'  => $pFld,
    'value'   => $pVal_base,
    'options' => $pOpts
  ];

  if ( count ($pOpts_trace) )
    $pTrace_data ['changed'] = $pOpts_trace;
  elseif ( $pVal <> $pVal_base )
    $pTrace_data ['result'] = $pVal;

  pFile_put_contents ($OccurDir  [$p] . "/fields/$pFldCnt.json", pJson ($pTrace_data ) );

?>