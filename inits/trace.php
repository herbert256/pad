<?php

  if ( isset($_REQUEST['pad_trace']) )
    $pad_trace = TRUE;

  if ( ! $pad_trace )
    return;

  $pad_parameters [1] ['trace_dir'] = $pad_trace_dir_lvl ; 
  $pad_parameters [1] ['occur_dir'] = $pad_trace_dir_occ ;
      
  pad_file_put_contents ($pad_trace_dir_base . "/1-start/dump.html",  pad_dump_get ('TRACE - start') );
  pad_file_put_contents ($pad_trace_dir_base . "/1-start/track.json", pad_json ( pad_track  () )     );
  pad_file_put_contents ($pad_trace_dir_base . "/1-start/php.json",   pad_dump_get_php_vars ()       );

?>