<?php

  $pSeq_operations      = $pSequence;
  $pSeq_operations_last = $pSequence;

  foreach ( $pSeq_opr as $pSeq_opr_name => $pSeq_opr_value ) {

    if ( in_array($pSeq_opr_name, $pSeq_one_done ) )
      continue;

    $pSeq_chk = PAD . "sequence/types/$pSeq_opr_name";

    $pSeq_parm_save = $pSeq_parm;

    pSeq_set ( $pSeq_opr_name, $pSeq_opr_value );

    $pSeq_loop = $pSeq_operations;

    if     ( in_array ( $pSeq_opr_name , $pSeq_special_ops ) ) $pSeq_operations = include "list.php";
    elseif ( file_exists ( "$pSeq_chk/make.php" )           ) $pSeq_operations = include "$pSeq_chk/make.php";
    elseif ( file_exists ( "$pSeq_chk/filter.php" )         ) $pSeq_operations = include "$pSeq_chk/filter.php";

    $pSeq_parm = $pSeq_parm_save;

    if     ( $pSeq_operations === FALSE ) return TRUE;   
    elseif ( $pSeq_operations === NULL  ) return FALSE;
    elseif ( $pSeq_operations === INF   ) return FALSE; 
    elseif ( $pSeq_operations === NAN   ) return FALSE; 
    elseif ( $pSeq_operations === TRUE  ) $pSeq_operations = $pSeq_operations_last;     

    $pSeq_operations_last = $pSeq_operations;
   
  }

  return $pSeq_operations;

?>