<?php

  if ( $GLOBALS['pTrace_headers'] ) 
    pFile_put_contents ($pTrace_dir . "/headers-in.json", getallheaders() );

  if ( isset($_REQUEST['pTrace']) )
    $pTrace = TRUE;

  if ( ! $pTrace )
    return;

  $pParms [1] ['level_dir'] = $pLevel_dir ; 
  $pParms [1] ['occur_dir'] = $pOccur_dir ;

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
      
  pFile_put_contents ($pTrace_dir . "/start.json",   $pTrace_data_start     );
  pFile_put_contents ($pTrace_dir . "/php.json",     pTrace_get_php_vars () );
  pFile_put_contents ($pTrace_dir . "/request.json", $_REQUEST                 );

?>