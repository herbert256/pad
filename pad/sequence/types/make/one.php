<?php

  $padSeq_parm_save = $padSeq_parm;

  foreach ( $GLOBALS ["padSeq_mf_$padSeq_seq"] as $padSeq_opt_name => $padSeq_opt_value ) {

    $padSeq_one_done [] = $padSeq_opt_name;

    $padSeq_parm = $padSeq_opt_value;

    $padSeq_opt_check = include PAD . "sequence/types/$padSeq_opt_name/$padSeq_filter_check.php"; 

    if ( $padSeq_seq == 'keep') { 
    
      if ( $padSeq_opt_check === FALSE ) {
        $padSeq_parm = $padSeq_parm_save;
        return FALSE;
      }
    
    } elseif ( $padSeq_seq == 'remove') { 
    
      if ( $padSeq_opt_check === TRUE ) {
        $padSeq_parm = $padSeq_parm_save;
        return FALSE;
      }
    
    } else {

      $padSeq_loop = $padSeq_opt_check;
    
    }

  }

  $padSeq_parm = $padSeq_parm_save;

  return $padSeq_loop;

?>