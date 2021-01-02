<?php
  
  $pad_between = "$name '$value'";

  foreach ($parm as $pad_k => $pad_v)
    $pad_between .= " | '$pad_v' ";

  include PAD_HOME . 'level/parms1.php';

  $pad_content   = '';
  $pad_false     = '';
  $pad_pair      = FALSE;
  $pad_name      = $pad_tag;
  $pad_parm      = $parm [0] ?? '';
  $pad_filter    = [];
  $oad_tag_count = 0;
  $pad_walk      = 'start';

  $pad_parms_org = $parm;
  $pad_parms_seq = $parm;
  $pad_parms_tag = [];
  $pad_parms_val = $parm;
 
  $pad_lvl = $GLOBALS['pad_lvl'] + 1;

  if ( isset ( $pad_current [$pad_lvl] ) )
    unset ( $pad_current [$pad_lvl] );

  $pad_walks       [$pad_lvl] = '';
  $pad_walks_data  [$pad_lvl] = [];
  $pad_current     [$pad_lvl] = [];
  $pad_parameters  [$pad_lvl] = []; 
  $pad_base        [$pad_lvl] = '';
  $pad_occur       [$pad_lvl] = 0;
  $pad_result      [$pad_lvl] = '';
  $pad_html        [$pad_lvl] = '';
  $pad_db          [$pad_lvl] = '';
  $pad_db_lvl      [$pad_lvl] = [];
  $pad_save_vars   [$pad_lvl] = [];
  $pad_delete_vars [$pad_lvl] = [];

  $pad_data [$pad_lvl] [1] = [];

  $result = include PAD_HOME . "types/$pad_tag_type.php";

  if ( in_array ( $pad_walk, ['next', 'end', 'occurence'] ) ) {

    $pad_html [$pad_lvl] = $pad_html [$pad_lvl] = $pad_html [$pad_lvl] = $pad_content;
    
    $result = include PAD_HOME . "types/$pad_tag_type.php";
  
  }

  if ( ! pad_is_default_data ( $pad_data [$pad_lvl] ) )
    return $pad_data [$pad_lvl];

  if ( $pad_content !== '' )
    return $pad_content;

  return $result;

?>