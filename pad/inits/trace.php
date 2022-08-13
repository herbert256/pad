<?php

  if ( isset($_REQUEST['pTrace']) )
    $pTrace = TRUE;

  $pTraceDir = "trace/$app-" . str_replace('/', '-', $page) . "/$PADREQID";

  if ( ! $pTrace )
    return;

  $pLevelDir [$p] = $pTraceDir; 
  $pOccurDir [$p] = $pTraceDir;

  $pTrace_data = [
    'app'         => $GLOBALS ['app'] ?? '',
    'page'        => $GLOBALS ['page'] ?? '',
    'sessionID'   => $GLOBALS ['PADSESSID'] ?? '',
    'requestID'   => $GLOBALS ['PADREQID'] ?? '',
    'referenceID' => $GLOBALS ['PADREFID'] ?? '',
    'start'       => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0,
    'uri'         => $_SERVER ['REQUEST_URI']     ?? '' ,
    'referer'     => $_SERVER ['HTTP_REFERER']    ?? '' ,
    'remote'      => $_SERVER ['REMOTE_ADDR']     ?? '' ,
    'agent'       => $_SERVER ['HTTP_USER_AGENT'] ?? '' ,
    'headers'     => getallheaders() ?? '' ,
    'request'     => $_REQUEST ?? '' ,
    'server'      => $_SERVER ?? '' ,
    'environment' => $_ENV ?? '' 
  ];
       
  pFile_put_contents ( $pTraceDir . "/start.json", $pTrace_data );

?>