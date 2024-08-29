<?php
  
  $padSeqOptions = [];

  foreach ( $padOptionsSingle as $padPrmName => $padPrmValue )

    if ( ! file_exists ( "/pad/sequence/actions/types/$padPrmName.php" ) 
     and ! file_exists ( "/pad/sequence/types/$padPrmName" ) 
     and   file_exists ( "/pad/sequence/options/types/$padPrmName.php" ) 
     and ! isset       ( $padSeqStore [$padPrmName] ) )

      $padSeqOptions [$padPrmName] = [$padPrmValue];
  
?>