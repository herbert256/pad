<?php

  if ( file_exists ( "sequence/types/$pqSeq/exit.php" ) )   
    include "sequence/types/$pqSeq/exit.php";    

  include 'sequence/exits/actions.php';
  include 'sequence/exits/store/store.php';
  include 'sequence/exits/data.php';
  include 'sequence/exits/return/return.php';
  include 'sequence/exits/done.php';
 
  if ( $padInfo ) {
    include 'sequence/exits/options.php';
    include 'events/sequence.php';
  }

?>