<?php

  if ( isset($_REQUEST['pTrace']) )
    $pTrace = TRUE;

  if ( ! $pTrace )
    return;

  $pTraceDir = "trace/$app-" . str_replace('/', '-', $page) . "/$PADREQID";

  $pLevelDir [$p] = $pTraceDir; 
  $pOccurDir [$p] = $pTraceDir;

  $pTrace_data_start = [
    'sessionID'   => $GLOBALS ['PADSESSID'] ?? '',
    'requestID'   => $GLOBALS ['PADREQID'] ?? '',
    'referenceID' => $GLOBALS ['PADREFID'] ?? '',
    'app'         => $GLOBALS ['app'] ?? '',
    'page'        => $GLOBALS ['page'] ?? '',
    'start'       => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0,
    'uri'         => $_SERVER ['REQUEST_URI']     ?? '' ,
    'referer'     => $_SERVER ['HTTP_REFERER']    ?? '' ,
    'remote'      => $_SERVER ['REMOTE_ADDR']     ?? '' ,
    'agent'       => $_SERVER ['HTTP_USER_AGENT'] ?? ''
  ];
       
  pFile_put_contents ($pTraceDir . "/headers-in.json", getallheaders()        );
  pFile_put_contents ($pTraceDir . "/start.json",      $pTrace_data_start     );
  pFile_put_contents ($pTraceDir . "/php.json",        pTrace_get_php_vars () );
  pFile_put_contents ($pTraceDir . "/request.json",    $_REQUEST              );

?>