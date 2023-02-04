<?php

  if ( $padVal == $padValBase )

    $padTraceData = [ 
      'field' => '{' . $padBetween . '}',
      'nr'    => $padFldCnt,
      'value' => $padVal
    ];

  else

    $padTraceData = [ 
      'field'   => '{' . $padBetween . '}',
      'nr'      => $padFldCnt,
      'from '   => $padValBase,
      'to'      => $padVal,
      'options' => $padOptsTrace
    ];

  padFilePutContents ( $padOccurDir [$pad] . "/fields/$padFld-$padFldCnt.json", $padTraceData );

?>