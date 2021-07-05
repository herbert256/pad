<?php

  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/after/track.json", pad_json ( pad_track ($GLOBALS['pad_stop']) ) );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/after/dump.html",  pad_dump_get ('TRACE - end' )                 );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/after/pad.json",   pad_dump_get_pad_vars ()                      );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/after/app.json",   pad_dump_get_app_vars ()                      );
  pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/after/php.json",   pad_dump_get_php_vars ()                      );
 
?>