<?php

  $pad_lvl_cnt++;
  
  $pad_data [$pad_lvl]       = [];
  $pad_data [$pad_lvl] [999] = [];

  $pad_occur   [$pad_lvl] = 0;
  $pad_current [$pad_lvl] = [];

  $pad_parameters  [$pad_lvl] = [];
  $pad_walks       [$pad_lvl] = '';
  $pad_walks_data  [$pad_lvl] = [];
  $pad_current     [$pad_lvl] = [];
  $pad_base        [$pad_lvl] = '';
  $pad_occur       [$pad_lvl] = 0;
  $pad_result      [$pad_lvl] = '';
  $pad_html        [$pad_lvl] = '';
  $pad_db          [$pad_lvl] = '';
  $pad_db_lvl      [$pad_lvl] = [];
  $pad_save_vars   [$pad_lvl] = [];
  $pad_delete_vars [$pad_lvl] = [];

  $pad_set_set     [$pad_lvl] = [];
  $pad_set_save    [$pad_lvl] = [];
  $pad_set_delete  [$pad_lvl] = [];

  $pad_parameters  [$pad_lvl] ['tag']      = '';
  $pad_parameters  [$pad_lvl] ['tag_type'] = '';
  $pad_parameters  [$pad_lvl] ['name']     = '';

  $pad_trace_dir_lvl  = $pad_trace_dir_occ;
  $pad_trace_dir_lvl .= '/tag.' . $pad_lvl_cnt;
  $pad_trace_dir_lvl .= '.' . $pad_parameters[$pad_lvl] ['tag'];

  if ( $pad_parameters[$pad_lvl] ['tag'] <> $pad_parameters[$pad_lvl] ['name'] )
    $pad_trace_dir_lvl .= '.' . $pad_parameters[$pad_lvl] ['name'];

  $pad_parameters [$pad_lvl] ['trace_dir'] = $pad_trace_dir_lvl ;

  $pad_walk = 'start';

  $pad_options_done = [];

  include 'parms2.php';

  $pad_name = $pad_parms_tag ['name'] ?? $pad_tag;

  include 'parameters.php';  

?>