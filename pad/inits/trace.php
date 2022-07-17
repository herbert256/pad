<?php

  if ( $GLOBALS['pad_trace_headers'] ) 
    pad_file_put_contents ($pad_trace_dir_base . "/headers-in.json", getallheaders() );

  if ( isset($_REQUEST['pad_trace']) )
    $pad_trace = TRUE;

  if ( ! $pad_trace )
    return;

  $pad_parameters [1] ['trace_dir'] = $pad_trace_dir_lvl ; 
  $pad_parameters [1] ['occur_dir'] = $pad_trace_dir_occ ;

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
      
  pad_file_put_contents ($pad_trace_dir_base . "/start.json",   $pad_trace_data_start     );
  pad_file_put_contents ($pad_trace_dir_base . "/php.json",     pad_trace_get_php_vars () );
  pad_file_put_contents ($pad_trace_dir_base . "/request.json", $_REQUEST                 );

?>