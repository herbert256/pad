<?php

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( str_starts_with ( $padPrmName, 'store' ) 
      include '/pad/sequence/store/store.php';

    if ( file_exists ( "/pad/sequence/actions/types/$padPrmName.php" ) ) {
      include '/pad/sequence/actions/action.php';

  }
 
?>