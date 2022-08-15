<?php

  $padSeq_operations      = $padSequence;
  $padSeq_operations_last = $padSequence;

  foreach ( $padSeq_opr as $padSeq_opr_name => $padSeq_opr_value ) {

    if ( in_array($padSeq_opr_name, $padSeq_one_done ) )
      continue;

    $padSeq_chk = PAD . "sequence/types/$padSeq_opr_name";

    $padSeq_parm_save = $padSeq_parm;

    pSeq_set ( $padSeq_opr_name, $padSeq_opr_value );

    $padSeq_loop = $padSeq_operations;

    if     ( in_array ( $padSeq_opr_name , $padSeq_special_ops ) ) $padSeq_operations = include "list.php";
    elseif ( file_exists ( "$padSeq_chk/make.php" )           ) $padSeq_operations = include "$padSeq_chk/make.php";
    elseif ( file_exists ( "$padSeq_chk/filter.php" )         ) $padSeq_operations = include "$padSeq_chk/filter.php";

    $padSeq_parm = $padSeq_parm_save;

    if     ( $padSeq_operations === FALSE ) return TRUE;   
    elseif ( $padSeq_operations === NULL  ) return FALSE;
    elseif ( $padSeq_operations === INF   ) return FALSE; 
    elseif ( $padSeq_operations === NAN   ) return FALSE; 
    elseif ( $padSeq_operations === TRUE  ) $padSeq_operations = $padSeq_operations_last;     

    $padSeq_operations_last = $padSeq_operations;
   
  }

  return $padSeq_operations;

?>