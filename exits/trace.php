<?php

  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/3-end/track.json",  pad_json ( pad_track () )     );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/3-end/dump.html",   pad_dump_get ('TRACE - end' ) );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/3-end/result.html", $GLOBALS['pad_result'][1]     );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/3-end/pad.json",    pad_dump_get_pad_vars ()      );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/3-end/app.json",    pad_dump_get_app_vars ()      );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/3-end/php.json",    pad_dump_get_php_vars ()      );
 
?>