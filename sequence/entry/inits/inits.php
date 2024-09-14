<?php

  include '/pad/sequence/entry/inits/clear.php';
  include '/pad/sequence/entry/inits/vars.php';

  $padSeqEntryExplode = padExplode ( $padParm, '|' ); 
  $padSeqEntryFirst   = $padSeqEntryExplode [0] ?? '';

  if ( count ( $padSeqEntryExplode ) )
    unset ( $padSeqEntryExplode [0] );

  $padSeqEntryRest = implode ( '|', $padSeqEntryExplode );

?>