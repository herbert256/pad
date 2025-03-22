<?php

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( file_exists ( "sequence/options/types/$padPrmName.php" ) )
      continue;

    if ( isset ( $padSeqDone [$padPrmName] ) )
      continue;

    $padSeqInfo ['options'] [] = $padPrmName;

    $padSeqOptions [] = [ 
      'padPrmName'  => $padPrmName,
      'padPrmValue' => $padPrmValue
    ];

  } 

?>