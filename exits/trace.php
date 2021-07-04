<?php

  pad_file_put_contents ($GLOBALS['pad_trace_dir_base'] . "/end.json",  pad_json ( pad_track ($GLOBALS['pad_stop']) ) );
  pad_file_put_contents ($GLOBALS['pad_trace_dir_base'] . "/output.html", $GLOBALS['pad_output']                        );
  pad_file_put_contents ($GLOBALS['pad_trace_dir_base'] . "/end.html",    pad_get_info ('TRACE - end' )                 );

?>