<?php
  
  if ( $pqPull )
    $pqBuild = 'pull';

  if ( ! $pqBuild ) 
    $pqBuild = pqBuild ( $pqSeq, 'loop' );

?>
