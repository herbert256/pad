<?php

  if ( ! $GLOBALS ['padTrace'] )
    return;

  $padTraceData = [
    'stop'    => $GLOBALS ['padStop'] ?? '',
    'length'  => $GLOBALS ['padLen'] ?? 0,
    'etag'    => $GLOBALS ['padEtag'] ?? '',
    'time'    => padDuration(),
  ];

  padTraceFields ( $padFphp, $padFlvl, $padFapp, $padFcfg, $padFpad, $padFids );

  padFilePutContents ( $GLOBALS ['padTraceDir'] . "/end.json",         $padTraceData );
  padFilePutContents ( $GLOBALS ['padTraceDir'] . "/result.html",      $GLOBALS ['padResult'][0] ?? '');
  padFilePutContents ( $GLOBALS ['padTraceDir'] . "/headers-out.json", $GLOBALS ['padHeaders']   ?? '');
  padFilePutContents ( $GLOBALS ['padTraceDir'] . "/pad.json",         $padFpad  );
  padFilePutContents ( $GLOBALS ['padTraceDir'] . "/app.json",         $padFapp  );
 
?>