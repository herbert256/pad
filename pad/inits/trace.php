<?php

  if ( isset($_REQUEST['pTrace']) )
    $padTrace = TRUE;

  $padTraceDir = "trace/$app-" . str_replace('/', '-', $page) . "/$PADREQID";

  if ( ! $padTrace )
    return;

  $padTraceData = [
    'app'         => $GLOBALS ['app'] ?? '',
    'page'        => $GLOBALS ['page'] ?? '',
    'sessionID'   => $GLOBALS ['PADSESSID'] ?? '',
    'requestID'   => $GLOBALS ['PADREQID'] ?? '',
    'referenceID' => $GLOBALS ['PADREFID'] ?? '',
    'start'       => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0,
    'uri'         => $_SERVER ['REQUEST_URI']     ?? '' ,
  ];


  pTraceFields ( $padFphp, $padFlvl, $padFapp, $padFcfg, $padFpad, $padFids );

  pFile_put_contents ( $GLOBALS ['padTraceDir'] . "/start.json",         $padTraceData );
  pFile_put_contents ( $GLOBALS ['padTraceDir'] . "/headers-in.json", getallheaders() );
  pFile_put_contents ( $GLOBALS ['padTraceDir'] . "/php.json",     $padFphp  );
  pFile_put_contents ( $GLOBALS ['padTraceDir'] . "/ids.json",     $padFids  );
  pFile_put_contents ( $GLOBALS ['padTraceDir'] . "/config.json",  $padFcfg  );

?>