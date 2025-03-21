<?php

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( ! file_exists ( "sequence/actions/types/$padPrmName.php" ) 
     and ! file_exists ( "sequence/types/$padPrmName" ) 
     and   file_exists ( "sequence/options/types/$padPrmName.php" ) 
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

  return;

    foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );
 
    if ( isset ( $padSeqNoNo [$padPrmName] ) ) {
      unset ( $padSeqNoNo [$padPrmName] );
      continue;
    }

    if ( file_exists ( "sequence/options/types/$padPrmName.php" ) ) {
       $padSeqInfo ['options'] [] = $padPrmName;
       continue;
    }

    $padSeqOptions [] = [ 
      'padPrmName'  => $padPrmName,
      'padPrmValue' => $padPrmValue
    ];

  } 
?>