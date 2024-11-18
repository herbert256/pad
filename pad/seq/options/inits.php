<?php

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( ! file_exists ( PAD . "seq/actions/types/$padPrmName.php" ) 
     and ! file_exists ( PAD . "seq/types/$padPrmName" ) 
     and   file_exists ( PAD . "seq/options/types/$padPrmName.php" ) 
     and ! isset       ( $padSeqStore [$padPrmName] ) )

       $padSeqInfo ['options'] [] = $padPrmName;

    if ( isset ( $padSeqNoNo [$padPrmName] ) ) {
      unset ( $padSeqNoNo [$padPrmName] );
      continue;
    }

    $padSeqOptions [] = [ 
      'padPrmName'  => $padPrmName,
      'padPrmValue' => $padPrmValue
    ];

  } 

?>