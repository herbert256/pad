<?php

  $pSeq_parm_save = $pSeq_parm;

  foreach ( $GLOBALS ["pSeq_mf_$pSeq_seq"] as $pSeq_opt_name => $pSeq_opt_value ) {

    $pSeq_one_done [] = $pSeq_opt_name;

    $pSeq_parm = $pSeq_opt_value;

    $pSeq_opt_check = include PAD . "sequence/types/$pSeq_opt_name/$pSeq_filter_check.php"; 

    if ( $pSeq_seq == 'keep') { 
    
      if ( $pSeq_opt_check === FALSE ) {
        $pSeq_parm = $pSeq_parm_save;
        return FALSE;
      }
    
    } elseif ( $pSeq_seq == 'remove') { 
    
      if ( $pSeq_opt_check === TRUE ) {
        $pSeq_parm = $pSeq_parm_save;
        return FALSE;
      }
    
    } else {

      $pSeq_loop = $pSeq_opt_check;
    
    }

  }

  $pSeq_parm = $pSeq_parm_save;

  return $pSeq_loop;

?>