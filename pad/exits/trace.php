<?php

  if ( $GLOBALS['pad_trace_headers'] )
    pad_file_put_contents ( $GLOBALS['pad_trace_dir'] . "/headers-out.json", $GLOBALS ['pad_headers'] );

  if ( ! $GLOBALS['pad_trace'] )
    return;

  $GLOBALS['pad_trace_data'] = [
    'stop'    => $GLOBALS ['pad_stop'] ?? '',
    'length'  => $GLOBALS ['pad_len'] ?? 0,
    'etag'    => $GLOBALS ['pad_etag'] ?? '',
    'time'    => microtime(true) - ($_SERVER ['REQUEST_TIME_FLOAT'] ?? 0),
  ];

  pad_file_put_contents ( $GLOBALS['pad_trace_dir'] . "/end.json",         $GLOBALS['pad_trace_data'] );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir'] . "/html-result.html", $GLOBALS['pad_result'][1]  );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir'] . "/pad.json",         pad_trace_get_pad_vars ()  );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir'] . "/app.json",         pad_trace_get_app_vars ()  );
 
?>