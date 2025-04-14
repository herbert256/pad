<?php

  if ( $padSeqContinue )
    return include 'sequence/continue/store.php';
  
  if ( $padPair [$pad] and ! $padSeqPush )
    return; 

  include 'sequence/exits/store/last.php';
  include 'sequence/exits/store/check.php';
  include 'sequence/exits/store/set.php';

?>