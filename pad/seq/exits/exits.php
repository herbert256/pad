<?php

  if ( file_exists ( "seq/types/$padSeqSeq/exit.php" ) )   
    include "seq/types/$padSeqSeq/exit.php";    

  include 'seq/exits/after.php';
  include 'seq/exits/store.php';
  include 'seq/exits/data.php';
  include 'seq/exits/return.php';
  include 'seq/exits/done.php';
   
  if ( $padInfo ) 
    include 'events/seq.php';

?>