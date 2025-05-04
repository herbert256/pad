<?php
  
  $pqTmp = constant ( "PAD$pqSeq" );

  if ( in_array ( $pqLoop, $pqTmp ) )
    return $pqTmp [$pqLoop];
  else
    return FALSE;
  
?>