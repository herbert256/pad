<?php

  if ( $pad_trace_occurence ) 
    include 'trace/end.php';
  
  $pad_result [$pad_lvl] .= $pad_html [$pad_lvl];

  $pad_trace_dir_occ = $pad_trace_dir_lvl;
  $pad_parameters [$pad_lvl] ['occur_dir'] = $pad_trace_dir_occ ;

  pad_reset ($pad_lvl);

?>