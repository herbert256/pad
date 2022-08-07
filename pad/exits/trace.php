<?php

  if ( $GLOBALS['pTrace_headers'] )
    pFile_put_contents ( $GLOBALS['pTrace_dir'] . "/headers-out.json", $GLOBALS ['pHeaders'] );

  if ( ! $GLOBALS['pTrace'] )
    return;

  $GLOBALS['pTrace_data'] = [
    'stop'    => $GLOBALS ['pStop'] ?? '',
    'length'  => $GLOBALS ['pLen'] ?? 0,
    'etag'    => $GLOBALS ['pEtag'] ?? '',
    'time'    => microtime(true) - ($_SERVER ['REQUEST_TIME_FLOAT'] ?? 0),
  ];

  pFile_put_contents ( $GLOBALS['pTrace_dir'] . "/end.json",         $GLOBALS['pTrace_data'] );
  pFile_put_contents ( $GLOBALS['pTrace_dir'] . "/html-result.html", $GLOBALS['pResult'][1]  );
  pFile_put_contents ( $GLOBALS['pTrace_dir'] . "/pad.json",         pTrace_get_pVars ()  );
  pFile_put_contents ( $GLOBALS['pTrace_dir'] . "/app.json",         pTrace_get_app_vars ()  );
 
?>