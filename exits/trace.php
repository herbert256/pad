<?php

  if ( ! isset ($GLOBALS['pad_trace'] ) )
    return;

  if ( ! $GLOBALS['pad_trace'] )
    return;

  if ( ! isset ( $GLOBALS ['pad_trace_data_start'] ) )
    return;

  $GLOBALS['pad_trace_data'] = [
    'stop'    => $GLOBALS ['pad_stop'] ?? '',
    'length'  => $GLOBALS ['pad_len'] ?? 0,
    'etag'    => $GLOBALS ['pad_etag'] ?? '',
    'time'    => microtime(true) - ($_SERVER ['REQUEST_TIME_FLOAT'] ?? 0),
    'headers' => $GLOBALS ['pad_headers'] ?? [],
    'timings' => $GLOBALS ['pad_timings']
  ];

  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/end.json",         $GLOBALS['pad_trace_data'] );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/result.html",      $GLOBALS['pad_result'][1]  );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/pad.json",         pad_dump_get_pad_vars ()   );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/app.json",         pad_dump_get_app_vars ()   );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/headers-out.json", $GLOBALS ['pad_headers']   );
 
?>