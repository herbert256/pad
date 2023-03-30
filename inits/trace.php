<?php

  if ( isset($_REQUEST['padTrace']) )
    $padTrace = TRUE;

  $GLOBALS ['padTraceDir'] = "trace/$padApp-" . str_replace('/', '-', $padPage) . "/$padReqID";

  if ( ! $padTrace )
    return;

  $padTraceData = [
    'padApp'      => $GLOBALS ['padApp'] ?? '',
    'padPage'     => $GLOBALS ['padPage'] ?? '',
    'sessionID'   => $GLOBALS ['padSesID'] ?? '',
    'requestID'   => $GLOBALS ['padReqID'] ?? '',
    'referenceID' => $GLOBALS ['padRefID'] ?? '',
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