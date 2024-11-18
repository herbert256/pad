<?php

  if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) 
    if ( ! file_exists ( PAD . "seq/actions/types/$padPrmName.php" ) )
      if ( ! file_exists ( PAD . "seq/types/$padPrmName" ) )
        if ( ! isset  ( $padSeqStore [$padPrmName] ) )
          if ( file_exists ( PAD . "options/$padPrmName.php" ) )
            padInfoXapp ( 'options', 'general', $padPrmName );
          elseif ( ! str_starts_with($padPrmName, 'store') )
            padInfoXapp ( 'options', 'specific', $padPrmName );
   
?>