<?php

  if ( isset($_REQUEST['pTrace']) )
    $pTrace = TRUE;

  $pTraceDir = "trace/$app-" . str_replace('/', '-', $page) . "/$PADREQID";

  if ( ! $pTrace )
    return;

  $pTraceData = [
    'app'         => $GLOBALS ['app'] ?? '',
    'page'        => $GLOBALS ['page'] ?? '',
    'sessionID'   => $GLOBALS ['PADSESSID'] ?? '',
    'requestID'   => $GLOBALS ['PADREQID'] ?? '',
    'referenceID' => $GLOBALS ['PADREFID'] ?? '',
    'start'       => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0,
    'uri'         => $_SERVER ['REQUEST_URI']     ?? '' ,
  ];


  pFields ( $pFphp, $pFlvl, $pFapp, $pFcfg, $pFpad, $pFids );

  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/start.json",         $pTraceData );
  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/headers-in.json", getallheaders() );
  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/php.json",     $pFphp  );
  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/ids.json",     $pFids  );
  pFile_put_contents ( $GLOBALS['pTraceDir'] . "/config.json",  $pFcfg  );

?>