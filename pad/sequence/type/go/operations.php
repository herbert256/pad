<?php

  $pad_seq_operations      = $pad_sequence;
  $pad_seq_operations_last = $pad_sequence;

  foreach ( $pad_seq_opr as $pad_seq_opr_name => $pad_seq_opr_value ) {

    if ( in_array($pad_seq_opr_name, $pad_seq_one_done ) )
      continue;

    $pad_seq_chk = PAD . "sequence/types/$pad_seq_opr_name";

    $pad_seq_parm_save = $pad_seq_parm;

    pad_seq_set ( $pad_seq_opr_name, $pad_seq_opr_value );

    $pad_seq_loop = $pad_seq_operations;

    if     ( in_array ( $pad_seq_opr_name , $pad_seq_special_ops ) ) $pad_seq_operations = include "list.php";
    elseif ( file_exists ( "$pad_seq_chk/make.php" )           ) $pad_seq_operations = include "$pad_seq_chk/make.php";
    elseif ( file_exists ( "$pad_seq_chk/filter.php" )         ) $pad_seq_operations = include "$pad_seq_chk/filter.php";

    $pad_seq_parm = $pad_seq_parm_save;

    if     ( $pad_seq_operations === FALSE ) return TRUE;   
    elseif ( $pad_seq_operations === NULL  ) return FALSE;
    elseif ( $pad_seq_operations === INF   ) return FALSE; 
    elseif ( $pad_seq_operations === NAN   ) return FALSE; 
    elseif ( $pad_seq_operations === TRUE  ) $pad_seq_operations = $pad_seq_operations_last;     

    $pad_seq_operations_last = $pad_seq_operations;
   
  }

  return $pad_seq_operations;

?>