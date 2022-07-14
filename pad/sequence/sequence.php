<?php

  $pad_in_sequence = TRUE;
  $pad_seq_store_get = '';
  $pad_seq_result = $pad_seq_for = $pad_seq_cache = [];
  $pad_seq = $pad_sequence = $pad_seq_protect_cnt = $pad_seq_base = 0;

  include_once "lib/sequence.php";
  include 'build/parms.php';
  include 'build/sequence.php';

  if ( ! $pad_seq_name )                            $pad_seq_name = $pad_seq_set; 
  if ( ! isset($GLOBALS ["pad_seq_$pad_seq_seq"]) ) $GLOBALS ["pad_seq_$pad_seq_seq"] = $pad_seq_parm;
  if ( ! isset($pad_parms_tag ["$pad_seq_seq"])   ) $pad_parms_tag ["$pad_seq_seq"]   = $pad_seq_parm;

  include 'build/loop.php';
  include 'build/lists.php';
  include 'build/operations.php';
  include 'build/page.php';
  include 'build/done.php';

  $pad_seq_build = include 'build/build.php';  

  if ( $pad_seq_build == 'function' ) include_once "types/$pad_seq_seq/function.php";
  if ( $pad_seq_build == 'bool'     ) include_once "types/$pad_seq_seq/bool.php";

  if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/init.php" )) 
    include "types/$pad_seq_seq/init.php";
      
  include 'type/type.php';
  include 'build/actions.php';
 
  if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/exit.php" ) )   
    $pad_seq_result = include "types/$pad_seq_seq/exit.php";

  include 'build/push.php';
  
  $pad_name = $pad_parameters [$pad_lvl] ['name'] = $pad_seq_name;

  $pad_in_sequence = FALSE;

  return $pad_seq_result;

?>