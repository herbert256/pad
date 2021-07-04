<?php

  $pad_trace_dir_lvl = $pad_trace_dir_base;

  for ($pad_k = 1; $pad_k <= $pad_lvl; $pad_k++)
    $pad_trace_dir_lvl .= "/$pad_k." . $pad_parameters[$pad_k] ['tag'] . '.' . $pad_parameters[$pad_k] ['tag_type'];

  $pad_parameters [$pad_lvl] ['trace_dir'] = $pad_trace_dir_lvl ;

  pad_file_put_contents ("$pad_trace_dir_lvl/parameters.json", pad_json ($pad_parameters [$pad_lvl] ) );
  pad_file_put_contents ("$pad_trace_dir_lvl/data.json",       pad_json ($pad_data[$pad_lvl]        ) );
  
  pad_trace ("level/start", "nr=$pad_lvl_cnt " . '{' . $pad_between . '}', TRUE);

?>