<?php

  if ( $GLOBALS['pad_trace_headers'] ) 
    pad_file_put_contents ($pad_trace_dir . "/headers-in.json", getallheaders() );

  if ( isset($_REQUEST['pad_trace']) )
    $pad_trace = TRUE;

  if ( ! $pad_trace )
    return;

  $pad_parms [1] ['level_dir'] = $pad_level_dir ; 
  $pad_parms [1] ['occur_dir'] = $pad_occur_dir ;

  $pad_trace_data_start = [
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
      
  pad_file_put_contents ($pad_trace_dir . "/start.json",   $pad_trace_data_start     );
  pad_file_put_contents ($pad_trace_dir . "/php.json",     pad_trace_get_php_vars () );
  pad_file_put_contents ($pad_trace_dir . "/request.json", $_REQUEST                 );

?>