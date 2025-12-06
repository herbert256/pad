<?php

  if ( $GLOBALS ['padInfoXref']  ) 
    if ( ! file_exists ( PAD . "sequence/actions/types/$padPrmName.php" ) )
      if ( ! file_exists ( PAD . "sequence/types/$padPrmName" ) )
        if ( ! isset  ( $pqStore [$padPrmName] ) )
          if ( file_exists ( PAD . "options/$padPrmName.php" ) )
            padInfoXref ( 'options', 'general', $padPrmName );
          else
            padInfoXref ( 'options', 'specific', $padPrmName );
   
?>