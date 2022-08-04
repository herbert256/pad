<?php

  if ( $pad_trace_occurence ) 
    include 'trace/end.php';
  
  $pad_result [$pad] .= $pad_html [$pad];

  $pad_trace_dir_occ = $pad_trace_dir_lvl;
  $pad_parms [$pad] ['occur_dir'] = $pad_trace_dir_occ ;

  pad_reset ($pad);

?>