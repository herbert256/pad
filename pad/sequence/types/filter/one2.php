<?php

  $pad_seq_parm_save = $pad_seq_parm;

  foreach ( $pad_seq_filter as $pad_seq_filter_name ) {

    $pad_seq_parm = $pad_parms_tag [$pad_seq_filter_name];

    $pad_seq_filter_check = include PAD . "sequence/types/$pad_seq_filter_name/filter.php"; 

    if ( $pad_seq_filter_check === FALSE ) {
      $pad_seq_parm = $pad_seq_parm_save;
      return FALSE;
    }

  }

  $pad_seq_parm = $pad_seq_parm_save;

  return $pad_seq_loop;

?>