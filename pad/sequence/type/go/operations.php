<?php

  foreach ( $pad_seq_opr as $pad_seq_opr_name => $pad_seq_opr_value ) {

    $pad_seq_loop = $pad_sequence;
  
    pad_seq_set ( $pad_seq_opr_name, $pad_seq_opr_value );

    if ( $pad_seq_opr_name == 'make' )
      $pad_sequence = include 'make.php';
    elseif ( $pad_seq_opr_name == 'filter' ) 
      $pad_sequence = include 'filter.php';
    elseif ( pad_file_exists ( PAD . "sequence/types/$pad_seq_opr_name/filter.php" ) )
      $pad_sequence = include PAD . "sequence/types/$pad_seq_opr_name/filter.php";
    elseif ( pad_file_exists ( PAD . "sequence/types/$pad_seq_opr_name/make.php" ) )
      $pad_sequence = include PAD . "sequence/types/$pad_seq_opr_name/make.php";

    if     ( $pad_sequence === NULL  ) return NULL;
    elseif ( $pad_sequence === FALSE ) return FALSE;   
    elseif ( $pad_sequence === INF   ) return INF; 
    elseif ( $pad_sequence === NAN   ) return NAN; 
    elseif ( $pad_sequence === TRUE  ) $pad_sequence = $pad_seq_loop;     
   
  }

  return $pad_sequence;

?>