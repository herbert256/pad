<?php

  $pTrace_data = [ 
    'field'   => '{' . $pBetween . '}',
    'nr'      => $pFld_cnt,
    'source'  => $pFld,
    'value'   => $pad_val_base,
    'options' => $pOpts
  ];

  if ( count ($pOpts_trace) )
    $pTrace_data ['changed'] = $pOpts_trace;
  elseif ( $pad_val <> $pad_val_base )
    $pTrace_data ['result'] = $pad_val;

  pFile_put_contents ("$pOccurDir/fields/$pFld_cnt.json", pJson ($pTrace_data ) );

?>