<?php

 if ( file_exists ( PAD . "sequence/types/$padSeq_seq/one.php" ) ) 
  
    $padSequence = include PAD . "sequence/types/$padSeq_seq/one.php";

  elseif ( $padSeq_build == 'fixed' ) 

    $padSequence = $padSeq_loop;

  elseif ( $padSeq_build == 'function' )

    $padSequence = "pSequence_$padSeq_seq" ($padSeq_loop);

  elseif ( $padSeq_build == 'bool' )

    $padSequence = include "bool.php";

  elseif ( $padSeq_random and file_exists ( PAD . "sequence/types/$padSeq_seq/random.php") )

    $padSequence = include PAD . "sequence/types/$padSeq_seq/random.php" ;

  else

    $padSequence = include PAD . "sequence/types/$padSeq_seq/$padSeq_build.php";

  if     ( $padSequence === NULL  ) return FALSE;
  elseif ( $padSequence === FALSE ) return TRUE;   
  elseif ( $padSequence === INF   ) return FALSE; 
  elseif ( $padSequence === NAN   ) return FALSE; 
  elseif ( $padSequence === TRUE  ) return $padSeq_loop;
  else                               return $padSequence;

?>