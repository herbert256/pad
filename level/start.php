<?php
  
  $pad_lvl++;
  $pad_lvl_cnt++;

  pad_trace ("level/start", "nr=$pad_lvl_cnt " . '{' . $pad_between . '}', TRUE);

  if ( isset ( $pad_current [$pad_lvl] ) )
    unset ( $pad_current [$pad_lvl] );

  $pad_walks       [$pad_lvl] = '';
  $pad_walks_data  [$pad_lvl] = [];
  $pad_current     [$pad_lvl] = [];
  $pad_parameters  [$pad_lvl] = [];
  $pad_data        [$pad_lvl] = []; 
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

  $pad_options_done = [];

  include PAD_HOME . 'level/parms2.php';

  $pad_name = $pad_parms_tag ['name'] ?? $pad_tag;

  $pad_parameters [$pad_lvl] ['name']             = $pad_name;
  $pad_parameters [$pad_lvl] ['tag']              = $pad_tag;
  $pad_parameters [$pad_lvl] ['tag_type']         = $pad_tag_type;
  $pad_parameters [$pad_lvl] ['pair']             = $pad_pair;
  $pad_parameters [$pad_lvl] ['content']          = $pad_content;
  $pad_parameters [$pad_lvl] ['false']            = $pad_false;
  $pad_parameters [$pad_lvl] ['pad_options_done'] = $pad_options_done;
  $pad_parameters [$pad_lvl] ['parms']            = $pad_parms;
  $pad_parameters [$pad_lvl] ['between']          = $pad_between;
  $pad_parameters [$pad_lvl] ['parms_type']       = $pad_parms_type;
  $pad_parameters [$pad_lvl] ['tag_count']        = 0;
 
  $pad_walk = 'start';
  $pad_tag_result = include PAD_HOME . 'level/tag.php';

  if ($pad_lvl > 1) 
    pad_data_chk ( $pad_data[$pad_lvl] );
  
  $pad_options = 'level_start';
  include PAD_HOME . "options/go/options.php";

  if ( isset($pad_parms_tag ['callback']) and ! isset($pad_parms_tag ['before']))
    include PAD_HOME . 'callback/init.php' ;

  if ( isset ( $pad_parms_tag ['parent'] ) )
    include PAD_HOME . 'options/parent.php';
  elseif ( $pad_pair === FALSE and $pad_tag_type == 'data' and trim($pad_base [$pad_lvl]) === '' and ! isset ( $pad_parms_tag ['print'] ) )
    include PAD_HOME . 'options/parent.php';

  if ( count ($pad_data[$pad_lvl] ) )
    include PAD_HOME . 'occurrence/start.php';
  else
    $pad_html [$pad_lvl] = '';

  return $pad_tag_result;
  
?>