<?php

  $padSeqReturn = [];

  if ( $padSeqNameGiven ) include 'sequence/exits/return/given.php'; 
  else                    include 'sequence/exits/return/names.php'; 

?>