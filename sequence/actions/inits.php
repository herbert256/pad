<?php
  
  $padSeqActions = [];

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( file_exists ( "/pad/sequence/actions/types/$padPrmName.php" ) ) {

      $padSeqActions [] = [
        'padSeqActionName'  => $padPrmName, 
        'padSeqActionValue' => $padPrmValue,
        'padSeqActionList'  => padExplode ( $padPrmValue, '|' ),
      ];

    }

  }
  
?>