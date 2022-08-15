<?php

  if ( $padVal == $padVal_base )

    $padTraceData = [ 
      'field' => '{' . $padBetween . '}',
      'nr'    => $padFldCnt,
      'value' => $padVal
    ];

  else

    $padTraceData = [ 
      'field'   => '{' . $padBetween . '}',
      'nr'      => $padFldCnt,
      'from '   => $padVal_base,
      'to'      => $padVal,
      'options' => $padOpts_trace
    ];

  pFile_put_contents ( $padOccurDir [$pad] . "/fields/$padFld-$padFldCnt.json", $padTraceData );

?>