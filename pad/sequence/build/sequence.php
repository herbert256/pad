<?php
 
  if ( in_array ( $pqBuild, ['keep', 'remove', 'flag'] ) ) {

    $pqBuildSave = $pqBuild;
    $pqPlay      = $pqBuild;
    $pqBuild     = 'check';

    include "sequence/plays/one.php";

    $pqBuild = $pqBuildSave;
    
  } elseif ( pqStore ( $pqBuild ) ) 
  
    $pq = $pqLoop;
  
  else

    $pq = include 'sequence/build/call.php';  
    
?>