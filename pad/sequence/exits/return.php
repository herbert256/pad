<?php

  $pqReturn = [];

  if ( $pqContinue )
    return;

  if ( $pqNameGiven ) include 'sequence/exits/return/given.php'; 
  else                    include 'sequence/exits/return/names.php'; 

?>