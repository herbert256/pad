<?php

  $padSeqSeqSave = $padSeqSeq;
  
  foreach ( $padSeqOptions as  $padSeqOption ) {

    extract (  $padSeqOption );

    if ( $padPrmName == $padSeqPull or $padPrmName == $padSeqSeq )
      continue;

    elseif ( $padPrmName == 'one' )

      include '/pad/sequence/one/one.php';

    elseif ( str_starts_with ( $padPrmName, 'action' ) )

      include '/pad/sequence/actions/action.php';

    elseif ( str_starts_with ( $padPrmName, 'store' ) )

      include '/pad/sequence/store/store.php';

    elseif ( file_exists ( "/pad/sequence/options/types/$padPrmName.php" ) )

      continue;

    elseif ( file_exists ( "/pad/sequence/actions/types/$padPrmName.php" ) )

      include '/pad/sequence/actions/action.php';

    elseif ( file_exists ( "/pad/sequence/one/types/$padPrmName.php" ) )

      include '/pad/sequence/one/one.php';

  }
 
  $padSeqSeq = $padSeqSeqSave;

?>