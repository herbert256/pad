<?php

  $pad_trace_dir_lvl  = $pad_trace_dir_occ;
  $pad_trace_dir_lvl .= '/lvl-' . $pad_parameters[$pad_lvl] ['lvl_cnt'];
  $pad_trace_dir_lvl .= '.' . $pad_parameters[$pad_lvl] ['tag'];

  if ( $pad_parameters[$pad_lvl] ['tag'] <> $pad_parameters[$pad_lvl] ['name'] )
    $pad_trace_dir_lvl .= '.' . $pad_parameters[$pad_lvl] ['name'];

  $pad_parameters [$pad_lvl] ['trace_dir'] = $pad_trace_dir_lvl ;

  pad_file_put_contents ("$pad_trace_dir_lvl/parameters.json", pad_json ($pad_parameters [$pad_lvl] ) );
  pad_file_put_contents ("$pad_trace_dir_lvl/data_now.json",   pad_json ($pad_data[$pad_lvl]        ) );  
  pad_file_put_contents ("$pad_trace_dir_lvl/data_stack.json", pad_json ($pad_current) );

?>