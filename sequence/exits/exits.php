<?php

  if ( file_exists ( "/pad/sequence/types/$padSeqSeq/exit.php" ) )   
    include "/pad/sequence/types/$padSeqSeq/exit.php";    

  include '/pad/sequence/exits/after.php';
  include '/pad/sequence/exits/store.php';
  include '/pad/sequence/exits/data.php';
  include '/pad/sequence/exits/return.php';
  include '/pad/sequence/exits/done.php';
   
  if ( $GLOBALS ['padInfo'] )
    include '/pad/events/sequence.php';

    foreach ( $padSeqInfo as $padSeqInfoKey => $padSeqInfoVal )
      foreach (  $padSeqInfoVal as $padSeqInfoVal2 )
        padInfoXapp ( 'sequence', $padSeqInfoKey, $padSeqInfoVal2 );
?>