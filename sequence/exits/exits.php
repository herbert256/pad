<?php

  if ( file_exists ( "/pad/sequence/types/$padSeqSeq/exit.php" ) )   
    include "/pad/sequence/types/$padSeqSeq/exit.php";    

  if ( $padSeqToData ) 
    include '/pad/sequence/exits/data.php';
  
  include '/pad/sequence/exits/store.php';
  include '/pad/sequence/exits/return.php';
   
  if ( $GLOBALS ['padInfo'] )
    include '/pad/events/sequence.php';

?>