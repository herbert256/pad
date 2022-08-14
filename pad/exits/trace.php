<?php

  if ( ! $GLOBALS['pTrace'] )
    return;

  $pTraceData = [
    'stop'    => $GLOBALS ['pStop'] ?? '',
    'length'  => $GLOBALS ['pLen'] ?? 0,
    'etag'    => $GLOBALS ['pEtag'] ?? '',
    'time'    => microtime(true) - ($_SERVER ['REQUEST_TIME_FLOAT'] ?? 0),
  ];

  pFields ( $pFphp, $pFlvl, $pFapp, $pFcfg, $pFpad, $pFids );

  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/end.json",         $pTraceData );
  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/result.html",  $GLOBALS['pResult'][0] ?? '');
  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/headers-out.json", $GLOBALS ['pHeaders']  ?? '');
  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/pad.json",     $pFpad  );
  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/app.json",     $pFapp  );
 
?>