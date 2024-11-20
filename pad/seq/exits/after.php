<?php

  $padSeqSeqSave = $padSeqSeq;
  
  foreach ( $padSeqOptions as  $padSeqOption ) {

    extract (  $padSeqOption );

    if ( $padPrmName == $padSeqPull or $padPrmName == $padSeqSeq )
      continue;

    elseif ( $padPrmName == 'one' )

      include 'seq/one/one.php';

    elseif ( str_starts_with ( $padPrmName, 'action' ) )

      include 'seq/actions/action.php';

    elseif ( str_starts_with ( $padPrmName, 'store' ) )

      include 'seq/store/store.php';  

    elseif ( file_exists ( PAD . "seq/options/types/$padPrmName.php" ) )

      continue;

    elseif ( file_exists ( PAD . "seq/actions/types/$padPrmName.php" ) )

      include 'seq/actions/action.php';

    elseif ( file_exists ( PAD . "seq/one/types/$padPrmName.php" ) )

      include 'seq/one/one.php';

  }
 
  $padSeqSeq = $padSeqSeqSave;

?>