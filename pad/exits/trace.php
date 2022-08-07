<?php

  if ( $GLOBALS['pTrace'] )
    pFile_put_contents ( $GLOBALS['pTraceDir'] . "/headers-out.json", $GLOBALS ['pHeaders'] );

  if ( ! $GLOBALS['pTrace'] )
    return;

  $GLOBALS['pTrace_data'] = [
    'stop'    => $GLOBALS ['pStop'] ?? '',
    'length'  => $GLOBALS ['pLen'] ?? 0,
    'etag'    => $GLOBALS ['pEtag'] ?? '',
    'time'    => microtime(true) - ($_SERVER ['REQUEST_TIME_FLOAT'] ?? 0),
  ];

  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/end.json",         $GLOBALS['pTrace_data'] );
  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/html-result.html", $GLOBALS['pResult'][1]  );
  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/pad.json",         pTrace_get_pVars ()  );
  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/app.json",         pTrace_get_app_vars ()  );
 
?>