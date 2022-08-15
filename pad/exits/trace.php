<?php

  if ( ! $GLOBALS ['padTrace'] )
    return;

  $padTraceData = [
    'stop'    => $GLOBALS ['padStop'] ?? '',
    'length'  => $GLOBALS ['padLen'] ?? 0,
    'etag'    => $GLOBALS ['padEtag'] ?? '',
    'time'    => microtime(true) - ($_SERVER ['REQUEST_TIME_FLOAT'] ?? 0),
  ];

  pTraceFields ( $padFphp, $padFlvl, $padFapp, $padFcfg, $padFpad, $padFids );

  pFile_put_contents ( $GLOBALS ['padTraceDir'] . "/end.json",         $padTraceData );
  pFile_put_contents ( $GLOBALS ['padTraceDir'] . "/result.html",  $GLOBALS ['padResult'][0] ?? '');
  pFile_put_contents ( $GLOBALS ['padTraceDir'] . "/headers-out.json", $GLOBALS ['padHeaders']  ?? '');
  pFile_put_contents ( $GLOBALS ['padTraceDir'] . "/pad.json",     $padFpad  );
  pFile_put_contents ( $GLOBALS ['padTraceDir'] . "/app.json",     $padFapp  );
 
?>