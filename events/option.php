<?php

  if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) 
    if ( ! file_exists ( "sequence/actions/types/$padPrmName.php" ) )
      if ( ! file_exists ( "sequence/types/$padPrmName" ) )
        if ( ! isset  ( $pqStore [$padPrmName] ) )
          if ( file_exists ( "options/$padPrmName.php" ) )
            padInfoXapp ( 'options', 'general', $padPrmName );
          else
            padInfoXapp ( 'options', 'specific', $padPrmName );
   
?>