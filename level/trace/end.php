<?php

  pad_trace ("level/end", "nr=$pad_lvl_cnt");

  pad_file_put_contents ("$pad_trace_dir_lvl/result.html", $pad_result[$pad_lvl] );

  if ($pad_lvl > 1) {
    $pad_trace_dir_lvl = $pad_parameters [$pad_lvl-1] ['trace_dir'];
    $pad_trace_dir_occ = $pad_parameters [$pad_lvl-1] ['occur_dir'];
  } 
  
?>