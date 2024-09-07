<?php

  $padSeqSeqSave = $padSeqSeq;
  
  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( str_starts_with ( $padPrmName, 'action' ) )

      include '/pad/sequence/after/actions/action.php';

    elseif ( str_starts_with ( $padPrmName, 'store' ) )

      include '/pad/sequence/after/store/store.php';

    elseif ( file_exists ( "/pad/sequence/after/actions/types/$padPrmName.php" ) )

      include '/pad/sequence/after/actions/action.php';

    elseif ( $padPrmName == 'single' )

      include '/pad/sequence/after/singles/single.php';

  }
 
  $padSeqSeq = $padSeqSeqSave;

?>