<?php
  
  $padSeqActions = [];

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( file_exists ( "/pad/sequence/actions/types/$padPrmName.php" ) ) {

      $padSeqActions [] = [
        'padSeqActionName'  => $padPrmName, 
        'padSeqActionList'  => padExplode ( $padPrmValue, '|' ),
      ];

    }

  }
  
?>