<?php

  foreach ( $pad_seq_opr as $pad_seq_opr_name => $pad_seq_opr_value ) {

    if ( in_array($pad_seq_opr_name, $pad_seq_one_done ) )
      continue;

    $pad_seq_chk = PAD . "sequence/types/$pad_seq_opr_name";

    $pad_seq_loop = $pad_sequence;

    $pad_seq_parm_save = $pad_seq_parm;
  
    pad_seq_set ( $pad_seq_opr_name, $pad_seq_opr_value );

    if     ( pad_file_exists ( "$pad_seq_chk/make.php" )   ) $pad_sequence = include "$pad_seq_chk/make.php";
    elseif ( pad_file_exists ( "$pad_seq_chk/filter.php" ) ) $pad_sequence = include "$pad_seq_chk/filter.php";
    else                                                     $pad_sequence = include "$pad_seq_opr_name.php";

    $pad_seq_parm = $pad_seq_parm_save;

    if     ( $pad_sequence === NULL  ) return NULL;
    elseif ( $pad_sequence === FALSE ) return FALSE;   
    elseif ( $pad_sequence === INF   ) return NULL; 
    elseif ( $pad_sequence === NAN   ) return NULL; 
    elseif ( $pad_sequence === TRUE  ) $pad_sequence = $pad_seq_loop;     
   
  }

  return $pad_sequence;

?>