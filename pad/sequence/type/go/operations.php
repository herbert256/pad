<?php

  foreach ( $pad_parms_tag as $pad_seq_one_name => $pad_seq_one_value ) {

    if ( $pad_seq_one_name <> $pad_seq_seq ) {

      $pad_seq_loop = $pad_sequence;
    
      if ( $pad_seq_one_value == 'make' )
        $pad_sequence = include 'make.php';
      elseif ( $pad_seq_one_value == 'filter' ) 
        $pad_sequence = include 'filter.php';
      elseif ( pad_file_exists ( PAD . "sequence/types/$pad_seq_one_name/make.php" ) )
        $pad_sequence = include PAD . "sequence/types/$pad_seq_one_name/make.php";
      elseif ( pad_file_exists ( PAD . "sequence/types/$pad_seq_one_name/filter.php" ) )
        $pad_sequence = include PAD . "sequence/types/$pad_seq_one_name/filter.php";
  
      if     ( $pad_sequence === NULL  ) return FALSE;
      elseif ( $pad_sequence === FALSE ) return TRUE;   
      elseif ( $pad_sequence === INF   ) return TRUE; 
      elseif ( $pad_sequence === NAN   ) return TRUE; 
      elseif ( $pad_sequence === TRUE  ) $pad_sequence = $pad_seq_loop;     
  
    }
 
  }

  return $pad_seq_sequence;

?>