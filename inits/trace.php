<?php

  if ( isset($_REQUEST['padTrace']) )
    $padTrace = TRUE;

  $GLOBALS ['padTraceDir'] = "trace/$app-" . str_replace('/', '-', $page) . "/$PADREQID";

  if ( ! $padTrace )
    return;

  $padTraceData = [
    'app'         => $GLOBALS ['app'] ?? '',
    'page'        => $GLOBALS ['page'] ?? '',
    'sessionID'   => $GLOBALS ['PADSESSID'] ?? '',
    'requestID'   => $GLOBALS ['PADREQID'] ?? '',
    'referenceID' => $GLOBALS ['PADREFID'] ?? '',
    'start'       => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0,
    'uri'         => $_SERVER ['REQUEST_URI'] ?? '' ,
  ];

  padTraceFields ( $padFphp, $padFlvl, $padFapp, $padFcfg, $padFpad, $padFids );

  padFilePutContents ( $GLOBALS ['padTraceDir'] . "/start.json",      $padTraceData );
  padFilePutContents ( $GLOBALS ['padTraceDir'] . "/headers-in.json", getallheaders() );
  padFilePutContents ( $GLOBALS ['padTraceDir'] . "/php.json",        $padFphp  );
  padFilePutContents ( $GLOBALS ['padTraceDir'] . "/ids.json",        $padFids  );
  padFilePutContents ( $GLOBALS ['padTraceDir'] . "/config.json",     $padFcfg  );

?>