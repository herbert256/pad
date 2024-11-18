<?php

  if ( file_exists ( PAD . "seq/types/$padSeqSeq/exit.php" ) )   
    include PAD . "seq/types/$padSeqSeq/exit.php";    

  include PAD . 'seq/exits/after.php';
  include PAD . 'seq/exits/store.php';
  include PAD . 'seq/exits/data.php';
  include PAD . 'seq/exits/return.php';
  include PAD . 'seq/exits/done.php';
   
  if ( $GLOBALS ['padInfo'] ) 
    include PAD . 'events/seq.php';

?>