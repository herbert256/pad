<?php
  
  $pad_lvl++;

  $pad_walks_data [ $pad_lvl] = [];

  $pad_parameters [$pad_lvl] ['name'] = '';
  $pad_parameters [$pad_lvl] ['parm'] = '';

  $pad_lvl_cnt++;

  pad_trace ("level/start", "nr=$pad_lvl_cnt " . '{' . $pad_between . '}' . " nr=$pad_lvl_cnt", TRUE);

  if ( isset ( $pad_current [$pad_lvl] ) )
    unset ( $pad_current [$pad_lvl] );

  $pad_walks       [$pad_lvl]   = '';
  $pad_current     [$pad_lvl]   = [];
  $pad_parameters  [$pad_lvl]   = [];
  $pad_data        [$pad_lvl]   = []; 
  $pad_base        [$pad_lvl]   = '';
  $pad_occur       [$pad_lvl]   = 0;
  $pad_result      [$pad_lvl]   = '';
  $pad_html        [$pad_lvl]   = '';
  $pad_db          [$pad_lvl]   = '';
  $pad_db_lvl      [$pad_lvl]   = [];
  $pad_save_vars   [$pad_lvl]   = [];
  $pad_delete_vars [$pad_lvl]   = [];

  $pad_filter = [];

  include PAD_HOME . 'level/parms.php';

  if ( isset ( $pad_parms_tag ['flag'] ) )
    include PAD_HOME . 'level/parms/flag.php';

  $pad_name = $pad_parms_tag ['name'] ?? $pad_tag;

  $pad_parameters [$pad_lvl] ['name']        = $pad_name;
  $pad_parameters [$pad_lvl] ['tag']         = $pad_tag;
  $pad_parameters [$pad_lvl] ['tag_type']    = $pad_tag_type;
  $pad_parameters [$pad_lvl] ['pair']        = $pad_pair;
  $pad_parameters [$pad_lvl] ['content']     = $pad_content;
  $pad_parameters [$pad_lvl] ['false']       = $pad_false;
  $pad_parameters [$pad_lvl] ['filter']      = $pad_filter;
  $pad_parameters [$pad_lvl] ['parms']       = $pad_parms;
  $pad_parameters [$pad_lvl] ['between']     = $pad_between;
  $pad_parameters [$pad_lvl] ['parms_type']  = $pad_parms_type;
  
  $pad_parameters [$pad_lvl] ['tag_count']   = 0;
 
  $pad_walk = 'start';
  include PAD_HOME . 'level/tag.php';

  if ($pad_lvl > 1) 
    pad_data_chk ( $pad_data[$pad_lvl] );
  
  foreach ($pad_parms_start as $pad_v)
    if ( isset ( $pad_parms_tag [$pad_v] ) )
      include PAD_HOME . "parms/$pad_v.php" ;

  if ( isset ( $pad_parms_tag ['callback'] ) ) {
    $pad_callback = "init_tag";
    include PAD_HOME . 'parms/callback.php';
  }

  if ( isset ( $pad_parms_tag ['parent'] ) )
    include PAD_HOME . 'parms/parent.php';
  elseif ( $pad_pair === FALSE and $pad_tag_type == 'data' and trim($pad_base [$pad_lvl]) === '' and ! isset ( $pad_parms_tag ['print'] ) )
    include PAD_HOME . 'parms/parent.php';

  if ( count ($pad_data[$pad_lvl] ) )
    include PAD_HOME . 'occurrence/start.php';
  else
    $pad_html [$pad_lvl] = '';

?>