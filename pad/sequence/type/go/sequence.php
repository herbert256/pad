<?php

 if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/one.php" ) ) 
  
    $pad_sequence = include PAD . "sequence/types/$pad_seq_seq/one.php";

  elseif ( $pad_seq_build == 'fixed' ) 

    $pad_sequence = $pad_seq_loop;

  elseif ( $pad_seq_build == 'function' )

    $pad_sequence = "pad_sequence_$pad_seq_seq" ($pad_seq_loop);

  elseif ( $pad_seq_build == 'bool' )

    $pad_sequence = include "bool.php";

  elseif ( $pad_seq_random and pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/random.php") )

    $pad_sequence = include PAD . "sequence/types/$pad_seq_seq/random.php" ;

  else

    $pad_sequence = include PAD . "sequence/types/$pad_seq_seq/$pad_seq_build.php";

  if     ( $pad_sequence === NULL  ) return FALSE;
  elseif ( $pad_sequence === FALSE ) return TRUE;   
  elseif ( $pad_sequence === INF   ) return FALSE; 
  elseif ( $pad_sequence === NAN   ) return FALSE; 
  elseif ( $pad_sequence === TRUE  ) return $pad_seq_loop;
  else                               return $pad_sequence;

?>