<?php

  if ( file_exists ( "/pad/sequence/types/$padSeqSeq/exit.php" ) )   
    include "/pad/sequence/types/$padSeqSeq/exit.php";    

  include '/pad/sequence/exits/actions.php';
  include '/pad/sequence/exits/data.php';
  include '/pad/sequence/exits/store.php';
  include '/pad/sequence/exits/return.php';
  include '/pad/sequence/exits/done.php';
  include '/pad/sequence/exits/check.php';
   
  if ( $GLOBALS ['padInfo'] )
    include '/pad/events/sequence.php';

?>