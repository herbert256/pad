<?php

  $pad_trace_dir_lvl  = $pad_trace_dir_occ;
  $pad_trace_dir_lvl .= '/tag.' . $pad_parameters[$pad_lvl] ['lvl_cnt'];
  $pad_trace_dir_lvl .= '.' . $pad_parameters[$pad_lvl] ['tag'];

  if ( $pad_parameters[$pad_lvl] ['tag'] <> $pad_parameters[$pad_lvl] ['name'] )
    $pad_trace_dir_lvl .= '.' . $pad_parameters[$pad_lvl] ['name'];

  $pad_parameters [$pad_lvl] ['trace_dir'] = $pad_trace_dir_lvl ;

  pad_file_put_contents ( "$pad_trace_dir_lvl/parameters.json", $pad_parameters [$pad_lvl] );
  pad_file_put_contents ( "$pad_trace_dir_lvl/data.json",       $pad_data[$pad_lvl]        );  
  pad_file_put_contents ( "$pad_trace_dir_lvl/pad.json",        pad_dump_get_pad_vars ()   );
  pad_file_put_contents ( "$pad_trace_dir_lvl/app.json",        pad_dump_get_app_vars ()   );
  pad_file_put_contents ( "$pad_trace_dir_lvl/base.html",       $pad_base[$pad_lvl]        );

?>