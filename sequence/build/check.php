<?php

  if ( file_exists ( PAD . "sequence/types/$pqSeq/bool.php" ) ) 

    return ( 'pqBool' . ucfirst($pqSeq) ) ( $pqLoop, $pqParm );
  
  elseif ( file_exists ( PAD . "sequence/types/$pqSeq/fixed.php" ) ) 

    return in_array ( $pqLoop, include PQ . "types/$pqSeq/fixed.php" );
  
  elseif ( file_exists ( PAD . "sequence/types/$pqSeq/build.php" ) ) 

    return in_array ( $pqLoop, include PQ . "types/$pqSeq/build.php" );
  
  elseif ( defined ( "PAD$pqSeq" ) ) 

    return in_array ( $pqLoop, constant ( "PAD$pqSeq" ) );
  
  return in_array ( $pqLoop, pqArray ( $pqSeq, $pqParm, "to=$pqLoop" ) );

?>