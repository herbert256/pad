<?php

  if ( file_exists ( PT . "$pqSeq/bool.php" ) ) 

    return ( 'pqBool' . ucfirst($pqSeq) ) ( $pqLoop, $pqParm );
  
  elseif ( file_exists ( PT . "$pqSeq/fixed.php" ) ) 

    return in_array ( $pqLoop, include PT . "$pqSeq/fixed.php" );
  
  elseif ( file_exists ( PT . "$pqSeq/build.php" ) ) 

    return in_array ( $pqLoop, include PT . "$pqSeq/build.php" );
  
  elseif ( defined ( "PAD$pqSeq" ) ) 

    return in_array ( $pqLoop, constant ( "PAD$pqSeq" ) );
  
  return in_array ( $pqLoop, pqArray ( $pqSeq, $pqParm, "to=$pqLoop" ) );

?>