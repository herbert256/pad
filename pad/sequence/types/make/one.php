<?php

  $pad_seq_parm_save = $pad_seq_parm;

  foreach ( $GLOBALS ["pad_seq_mf_$pad_seq_seq"] as $pad_seq_opt_name => $pad_seq_opt_value ) {

    $pad_seq_one_done [] = $pad_seq_opt_name;

    $pad_seq_parm = $pad_seq_opt_value;

    $pad_seq_opt_check = include PAD . "sequence/types/$pad_seq_opt_name/$pad_seq_filter_check.php"; 

    if ( $pad_seq_seq == 'keep') { 
    
      if ( $pad_seq_opt_check === FALSE ) {
        $pad_seq_parm = $pad_seq_parm_save;
        return FALSE;
      }
    
    } elseif ( $pad_seq_seq == 'remove') { 
    
      if ( $pad_seq_opt_check === TRUE ) {
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