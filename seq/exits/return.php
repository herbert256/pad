<?php

  $padSeqReturn = [];

  foreach ($padSeqResult as $padSeqValue  )
    $padSeqReturn [] ['seq'] = $padSeqValue; 

  if ( $GLOBALS ['padInfo'] )
    include '/pad/events/seqEnd.php';

?>