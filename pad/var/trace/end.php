<?php

  if ( $pVal == $pVal_base )

    $pTrace_data = [ 
      'field' => '{' . $pBetween . '}',
      'nr'    => $pFldCnt,
      'value' => $pVal
    ];

  else

    $pTrace_data = [ 
      'field'   => '{' . $pBetween . '}',
      'nr'      => $pFldCnt,
      'from '   => $pVal_base,
      'to'      => $pVal,
      'options' => $pOpts_trace
    ];

  pFile_put_contents ( $pOccurDir [$p] . "/fields/$pFldCnt.json", $pTrace_data );

?>