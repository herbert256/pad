<?php

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( ! file_exists ( "seq/actions/types/$padPrmName.php" ) 
     and ! file_exists ( "seq/types/$padPrmName" ) 
     and   file_exists ( "seq/options/types/$padPrmName.php" ) 
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