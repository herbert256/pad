<?php

  $pad_seq_parm_save = $pad_seq_parm;

  foreach ( $GLOBALS ["pad_seq_$pad_seq_seq"] as $pad_seq_opt_name ) {

    $pad_seq_parm = $pad_parms_tag [$pad_seq_opt_name];

    $pad_seq_opt_check = include PAD . "sequence/types/$pad_seq_opt_name/$pad_seq_seq.php"; 

    if ( $pad_seq_seq == 'filter') {
      if ( $pad_seq_opt_check === FALSE ) {
        $pad_seq_parm = $pad_seq_parm_save;
        return FALSE;
      }
    } else {
      $pad_seq_loop = $pad_seq_opt_check;

    }

  }

  $pad_seq_parm = $pad_seq_parm_save;

  return $pad_seq_loop;

?>