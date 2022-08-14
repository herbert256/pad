<?php

  if ( $pVal == $pVal_base )

    $pTraceData = [ 
      'field' => '{' . $pBetween . '}',
      'nr'    => $pFldCnt,
      'value' => $pVal
    ];

  else

    $pTraceData = [ 
      'field'   => '{' . $pBetween . '}',
      'nr'      => $pFldCnt,
      'from '   => $pVal_base,
      'to'      => $pVal,
      'options' => $pOpts_trace
    ];

  pFile_put_contents ( $pOccurDir [$p] . "/fields/$pFld-$pFldCnt.json", $pTraceData );

?>