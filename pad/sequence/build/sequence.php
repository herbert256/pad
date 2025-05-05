<?php
 
  $pq = include "sequence/build/main/$pqBuild.php";

  if     ( $pq === NULL ) $pq = FALSE;
  elseif ( $pq === INF  ) $pq = FALSE; 
  elseif ( $pq === NAN  ) $pq = FALSE; 

  $pqOrgName = $pqSeq;
  $pqOrgSet  = $pq;

  if ( $pqOrgSet === TRUE ) 
    $pqOrgSet = $pqLoop;
    
?>