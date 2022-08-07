<?php

 if ( file_exists ( PAD . "sequence/types/$pSeq_seq/one.php" ) ) 
  
    $pSequence = include PAD . "sequence/types/$pSeq_seq/one.php";

  elseif ( $pSeq_build == 'fixed' ) 

    $pSequence = $pSeq_loop;

  elseif ( $pSeq_build == 'function' )

    $pSequence = "pSequence_$pSeq_seq" ($pSeq_loop);

  elseif ( $pSeq_build == 'bool' )

    $pSequence = include "bool.php";

  elseif ( $pSeq_random and file_exists ( PAD . "sequence/types/$pSeq_seq/random.php") )

    $pSequence = include PAD . "sequence/types/$pSeq_seq/random.php" ;

  else

    $pSequence = include PAD . "sequence/types/$pSeq_seq/$pSeq_build.php";

  if     ( $pSequence === NULL  ) return FALSE;
  elseif ( $pSequence === FALSE ) return TRUE;   
  elseif ( $pSequence === INF   ) return FALSE; 
  elseif ( $pSequence === NAN   ) return FALSE; 
  elseif ( $pSequence === TRUE  ) return $pSeq_loop;
  else                               return $pSequence;

?>