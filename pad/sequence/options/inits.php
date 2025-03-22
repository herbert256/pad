<?php

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( file_exists ( "sequence/options/types/$padPrmName.php" ) )
      continue;

    if ( in_array ( $padPrmName, $padSeqDone ) )
      continue;

    $padSeqInfo ['options'] [] = $padPrmName;

    $padSeqOptions [] = [ 
      'padPrmName'  => $padPrmName,
      'padPrmValue' => $padPrmValue
    ];

  } 

?>