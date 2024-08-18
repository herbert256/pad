<?php

  if ( file_exists ( "/pad/sequence/types/$padSeqSeq/exit.php" ) )   
    include "/pad/sequence/types/$padSeqSeq/exit.php";    

  if     ( isset ( $padSeqToData)                     ) include '/pad/sequence/exits/data.php';
  elseif ( isset ($padSeqPop) or isset ($padSeqShift) ) include '/pad/sequence/exits/pushpop.php';
  elseif ( ! $padPair [$pad]                          ) include '/pad/sequence/exits/store.php';
  else                                                  include '/pad/sequence/exits/return.php';
   
  if ( $GLOBALS ['padInfo'] )
    include '/pad/events/sequence.php';

?>