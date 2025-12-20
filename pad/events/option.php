<?php

  global $padInfoXref;

  if ( $padInfoXref  )
    if ( ! file_exists ( PQ . "actions/types/$padPrmName.php" ) )
      if ( ! file_exists ( PT . "$padPrmName" ) )
        if ( ! isset  ( $pqStore [$padPrmName] ) )
          if ( file_exists ( PAD . "options/$padPrmName.php" ) )
            padInfoXref ( 'options', 'general', $padPrmName );
          else
            padInfoXref ( 'options', 'specific', $padPrmName );

?>