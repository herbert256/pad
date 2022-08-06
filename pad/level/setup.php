<?php
  
  include 'between.php';

  $pad_data        [$pad] = pad_default_data ();
  $pad_current     [$pad] = reset ( $pad_data[$pad] );
  $pad_key         [$pad] = key ( $pad_data[$pad] );

  $pad_walks       [$pad] = 'start';
  $pad_walks_data  [$pad] = [];
  
  $pad_done        [$pad] = [];
  $pad_occur       [$pad] = 0;
  $pad_start       [$pad] = 0;
  $pad_end         [$pad] = 0;
  $pad_db          [$pad] = '';
  $pad_db_lvl      [$pad] = [];

  $pad_save_vars   [$pad] = [];
  $pad_delete_vars [$pad] = [];
  $pad_set_save    [$pad] = [];
  $pad_set_delete  [$pad] = [];

  $pad_true        [$pad] = '';
  $pad_false       [$pad] = '';
  $pad_base        [$pad] = '';
  $pad_html        [$pad] = '';
  $pad_result      [$pad] = '';

  $pad_parms [$pad] ['prms_type'] = $pad_prms_type ?? 'open';
  $pad_parms [$pad] ['tag_cnt']   = 0;
  $pad_parms [$pad] ['pair']      = $pad_pair ?? FALSE;
  $pad_parms [$pad] ['type']      = $pad_parms [$pad] ['type'] ?? $pad_type ?? '';
  $pad_parms [$pad] ['hit']       = TRUE;
  $pad_parms [$pad] ['null']      = FALSE;
  $pad_parms [$pad] ['else']      = FALSE;
  $pad_parms [$pad] ['array']     = FALSE;
  $pad_parms [$pad] ['text']      = TRUE;
  $pad_parms [$pad] ['default']   = pad_is_default_data ( $pad_data [$pad] );
  $pad_parms [$pad] ['level_dir'] = $pad_level_dir;
  $pad_parms [$pad] ['occur_dir'] = $pad_occur_dir;

?>