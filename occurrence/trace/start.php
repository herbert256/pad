<?php

  if ($pad_lvl > 1)  
    $pad_trace_dir_occ = "$pad_trace_dir_lvl/occurrence-" . $pad_occur [$pad_lvl];

  $pad_parameters [$pad_lvl] ['occur_dir'] = $pad_trace_dir_occ ;

  if ( $pad_lvl > 1) {
    $pad_json = [ $pad_key [$pad_lvl] => $pad_current [$pad_lvl] ] ;
    pad_file_put_contents ("$pad_trace_dir_occ/base.html", $pad_base[$pad_lvl]   );
    pad_file_put_contents ("$pad_trace_dir_occ/data_now.json", pad_json ($pad_json ) );
    pad_file_put_contents ("$pad_trace_dir_occ/data_stack.json", pad_json ($pad_current) );
  }

?>