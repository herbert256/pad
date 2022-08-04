<?php

  if ( $pad_trace_occurence ) 
    include 'trace/end.php';
  
  $pad_result [$pad] .= $pad_html [$pad];

  $pad_occur_dir = $pad_level_dir;
  $pad_parms [$pad] ['occur_dir'] = $pad_occur_dir ;

  pad_reset ($pad);

?>