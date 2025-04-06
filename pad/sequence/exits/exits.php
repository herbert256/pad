<?php

  if ( file_exists ( "sequence/types/$padSeqSeq/exit.php" ) )   
    include "sequence/types/$padSeqSeq/exit.php";    

  include 'sequence/exits/after.php';
  include 'sequence/exits/store.php';
  include 'sequence/exits/data.php';
  include 'sequence/exits/return.php';
  include 'sequence/exits/done.php';
   

  if ( $padInfo ) {
    include 'sequence/exits/options.php';
    include 'events/sequence.php';
  }

?>