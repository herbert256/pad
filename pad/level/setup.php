<?php
  
  $pad_data    [$pad_lvl] = pad_default_data ();
  $pad_current [$pad_lvl] = reset ( $pad_data[$pad_lvl] );
  $pad_key     [$pad_lvl] = key ( $pad_data[$pad_lvl] );

  $pad_occur   [$pad_lvl] = 1;

  $pad_parameters  [$pad_lvl] = [];
  $pad_walks       [$pad_lvl] = 'start';
  $pad_walks_data  [$pad_lvl] = [];
  $pad_current     [$pad_lvl] = [];
  $pad_base        [$pad_lvl] = '';
  $pad_occur       [$pad_lvl] = 0;
  $pad_start       [$pad_lvl] = 0;
  $pad_end         [$pad_lvl] = 0;
  $pad_result      [$pad_lvl] = '';
  $pad_html        [$pad_lvl] = '';
  $pad_db          [$pad_lvl] = '';
  $pad_db_lvl      [$pad_lvl] = [];
  $pad_save_vars   [$pad_lvl] = [];
  $pad_delete_vars [$pad_lvl] = [];

  $pad_set_save    [$pad_lvl] = [];
  $pad_set_delete  [$pad_lvl] = [];

  $pad_parameters [$pad_lvl] = [];

  $pad_parameters [$pad_lvl] ['tag']  = 'pad';
  $pad_parameters [$pad_lvl] ['name'] = 'pad';

  include PAD . 'level/parms1.php';
  include PAD . 'level/parms2.php';

  $pad_name = $pad_parms_tag ['name'] ?? $pad_tag;

  $pad_parameters [$pad_lvl] ['parms']            = $pad_parms;
  $pad_parameters [$pad_lvl] ['between']          = $pad_between;

  $pad_parameters [$pad_lvl] ['tag']              = $pad_tag;
  $pad_parameters [$pad_lvl] ['name']             = $pad_name;
  $pad_parameters [$pad_lvl] ['tag_cnt']          = 0;
  $pad_parameters [$pad_lvl] ['pair']             = FALSE;
  $pad_parameters [$pad_lvl] ['tag_type']         = 'pad';
  $pad_parameters [$pad_lvl] ['content']          = '';
  $pad_parameters [$pad_lvl] ['false']            = '';
  $pad_parameters [$pad_lvl] ['pad_options_done'] = [];
  $pad_parameters [$pad_lvl] ['parms_type']       = $pad_parms_type;
  $pad_parameters [$pad_lvl] ['default_data']     = TRUE;

  $pad_parameters [$pad_lvl] ['trace_dir'] = $pad_trace_dir_base;
  $pad_parameters [$pad_lvl] ['occur_dir'] = $pad_trace_dir_base;

  $pad_parameters [$pad_lvl] ['content'] = $pad_content;
  $pad_parameters [$pad_lvl] ['false']   = $pad_false;
  $pad_parameters [$pad_lvl] ['true']    = TRUE;
  $pad_parameters [$pad_lvl] ['null']    = FALSE;
  $pad_parameters [$pad_lvl] ['else']    = FALSE;
  $pad_parameters [$pad_lvl] ['array']   = FALSE;
  $pad_parameters [$pad_lvl] ['text']    = TRUE;
  $pad_parameters [$pad_lvl] ['default'] = pad_is_default_data ( $pad_data [$pad_lvl] );

?>