<?php

  $GLOBALS ["pad_seq_mf_$pad_seq_seq"] = [];
 
  foreach ( $pad_prms_tag as $pad_seq_opt_name => $pad_seq_opt_value )

    if ( file_exists ( PAD . "sequence/types/$pad_seq_opt_name/$pad_seq_filter_check.php" ) ) {

      $GLOBALS ["pad_seq_mf_$pad_seq_seq"] [$pad_seq_opt_name] = $pad_seq_opt_value;

      pad_done (, $pad_seq_opt_name, TRUE );

      $GLOBALS ["pad_seq_$pad_seq_opt_name"] = $pad_seq_opt_value;

    }
   
  $pad_sequence_store_get = $pad_seq_parm;
  $pad_seq_for = $pad_sequence_store [$pad_seq_parm];

  if ( ! $pad_seq_push and ! $pad_pair )
    $pad_seq_push = $pad_seq_parm;

  include PAD . "sequence/type/for.php";
      
?>