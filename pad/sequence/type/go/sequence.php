<?php

 if ( file_exists ( PAD . "sequence/types/$padSeqSeq/one.php" ) ) 
  
    $padSequence = include PAD . "sequence/types/$padSeqSeq/one.php";

  elseif ( $padSeqBuild == 'fixed' ) 

    $padSequence = $padSeqLoop;

  elseif ( $padSeqBuild == 'function' )

    $padSequence = ( "padSeq" . ucfirst($padSeqSeq) ) ($padSeqLoop);

  elseif ( $padSeqBuild == 'bool' )

    $padSequence = include "bool.php";

  elseif ( $padSeqRandom and file_exists ( PAD . "sequence/types/$padSeqSeq/random.php") )

    $padSequence = include PAD . "sequence/types/$padSeqSeq/random.php" ;

  else

    $padSequence = include PAD . "sequence/types/$padSeqSeq/$padSeqBuild.php";

  if     ( $padSequence === NULL  ) return FALSE;
  elseif ( $padSequence === FALSE ) return TRUE;   
  elseif ( $padSequence === INF   ) return FALSE; 
  elseif ( $padSequence === NAN   ) return FALSE; 
  elseif ( $padSequence === TRUE  ) return $padSeqLoop;
  else                              return $padSequence;

?>