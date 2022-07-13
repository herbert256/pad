<?php

  $pad_in_sequence = TRUE;

  include_once "lib/sequence.php";

  include 'build/parms.php';

  $pad_seq_result = $pad_seq_for = $pad_seq_make_list = $pad_seq_cache = $pad_seq_filter_list = [];
  $pad_seq = $pad_sequence = $pad_seq_protect_cnt = $pad_seq_base = 0;

  include 'build/sequence.php';

  if ( ! $pad_seq_name ) 
    $pad_seq_name = $pad_seq_set; 

  include 'build/loop.php';
  include 'build/make.php';
  include 'build/filter.php';
  include 'build/operations.php';
  include 'build/page.php';

  if ( ! isset($GLOBALS ["pad_seq_$pad_seq_seq"]) ) $GLOBALS ["pad_seq_$pad_seq_seq"] = $pad_seq_parm;
  if ( ! isset($pad_parms_tag ["$pad_seq_seq"])   ) $pad_parms_tag ["$pad_seq_seq"]   = $pad_seq_parm;

  foreach ( $pad_parms_tag as $pad_seq_tag_name => $pad_seq_tag_value ) {
    if ( ! isset($GLOBALS ["pad_seq_$pad_seq_tag_name"]) )
      $GLOBALS ["pad_seq_$pad_seq_tag_name"] = $pad_seq_tag_value;
    if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_tag_name/make.php" ) )
      pad_set_arr_var ( 'options_done', $pad_seq_tag_name, TRUE );
    if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_tag_name/filter.php" ) )
      pad_set_arr_var ( 'options_done', $pad_seq_tag_name, TRUE );
  }

  include 'build/done.php';

  $pad_seq_build = include 'build/type.php';  

  if ( $pad_seq_build == 'function' ) include_once "types/$pad_seq_seq/function.php";
  if ( $pad_seq_build == 'bool'     ) include_once "types/$pad_seq_seq/bool.php";

  if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/init.php" )) 
    include PAD . "sequence/types/$pad_seq_seq/init.php";
      
  include 'type/type.php';

  include 'build/actions.php';
 
  if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/exits.php" ) )   
    $pad_seq_result = include PAD . "sequence/types/$pad_seq_seq/exits.php";

  include 'build/push.php';
  
  $pad_name = $pad_parameters [$pad_lvl] ['name'] = $pad_seq_name;

  $pad_in_sequence = FALSE;

  return $pad_seq_result;

?>