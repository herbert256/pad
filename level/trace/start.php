<?php

  $pad_trace_dir_lvl  = $pad_trace_dir_occ;
  $pad_trace_dir_lvl .= '/tag.' . $pad_parameters[$pad_lvl] ['lvl_cnt'];
  $pad_trace_dir_lvl .= '.' . $pad_parameters[$pad_lvl] ['tag'];

  $pad_trace_dir_occ = $pad_trace_dir_lvl;

  if ( $pad_parameters[$pad_lvl] ['tag'] <> $pad_parameters[$pad_lvl] ['name'] )
    $pad_trace_dir_lvl .= '.' . $pad_parameters[$pad_lvl] ['name'];

  $pad_parameters [$pad_lvl] ['trace_dir'] = $pad_trace_dir_lvl ;

  include PAD_HOME . 'level/trace/trace.php';

?>