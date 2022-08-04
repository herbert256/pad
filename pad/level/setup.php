<?php
  
  $pad_data        [$pad] = pad_default_data ();
  $pad_current     [$pad] = reset ( $pad_data[$pad] );
  $pad_key         [$pad] = key ( $pad_data[$pad] );
  $pad_parms  [$pad] = [];
  $pad_walks       [$pad] = 'start';
  $pad_walks_data  [$pad] = [];
  $pad_current     [$pad] = [];
  $pad_base        [$pad] = '';
  $pad_occur       [$pad] = 0;
  $pad_start       [$pad] = 0;
  $pad_end         [$pad] = 0;
  $pad_result      [$pad] = '';
  $pad_html        [$pad] = '';
  $pad_db          [$pad] = '';
  $pad_db_lvl      [$pad] = [];
  $pad_save_vars   [$pad] = [];
  $pad_delete_vars [$pad] = [];
  $pad_set_save    [$pad] = [];
  $pad_set_delete  [$pad] = [];

  $pad_parms [$pad] ['tag']  = '';
  $pad_parms [$pad] ['name'] = '';

  include PAD . 'level/parms1.php';
  include PAD . 'level/parms2.php';

  $pad_name = $pad_prms_tag ['name'] ?? $pad_tag;

  $pad_parms [$pad] ['tag']              = $pad_tag;
  $pad_parms [$pad] ['name']             = $pad_name;
  $pad_parms [$pad] ['parms']            = $pad_prms;
  $pad_parms [$pad] ['between']          = $pad_between;
  $pad_parms [$pad] ['tag_cnt']          = 0;
  $pad_parms [$pad] ['pair']             = $pad_pair ?? FALSE;
  $pad_parms [$pad] ['tag_type']         = $pad_parms [$pad] ['tag_type']  ?? $pad_tag_type ?? '';
  $pad_parms [$pad] ['pad_options_done'] = [];
  $pad_parms [$pad] ['parms_type']       = $pad_prms_type;

  $pad_parms [$pad] ['trace_dir'] = $pad_trace_dir_base;
  $pad_parms [$pad] ['occur_dir'] = $pad_trace_dir_base;

  $pad_parms [$pad] ['content'] = $pad_content ?? '';
  $pad_parms [$pad] ['false']   = $pad_false   ?? ''; 
  $pad_parms [$pad] ['true']    = TRUE;
  $pad_parms [$pad] ['null']    = FALSE;
  $pad_parms [$pad] ['else']    = FALSE;
  $pad_parms [$pad] ['array']   = FALSE;
  $pad_parms [$pad] ['text']    = TRUE;
  $pad_parms [$pad] ['default'] = pad_is_default_data ( $pad_data [$pad] );

?>