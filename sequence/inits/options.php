<?php
  
  $padSeqOptions = [];

  foreach ( $padStartOptionsSingle as $padPrmName => $padPrmValue )

    if ( ! file_exists ( "/pad/sequence/actions/types/$padPrmName.php" ) 
     and ! file_exists ( "/pad/sequence/types/$padPrmName" ) 
     and ! isset       ( $padSeqStore [$padPrmName] ) )

      $padSeqOptions [$padPrmName] = [$padPrmValue];
  
?>