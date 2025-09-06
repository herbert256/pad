<?php

  if ( $GLOBALS ['padInfoXref']  ) 
    if ( ! file_exists ( "sequence/actions/types/$padPrmName.php" ) )
      if ( ! file_exists ( "sequence/types/$padPrmName" ) )
        if ( ! isset  ( $pqStore [$padPrmName] ) )
          if ( file_exists ( "options/$padPrmName.php" ) )
            padInfoXref ( 'options', 'general', $padPrmName );
          else
            padInfoXref ( 'options', 'specific', $padPrmName );
   
?>