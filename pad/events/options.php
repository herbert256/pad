<?php

  if ( $padTagSeq [$pad] )
    return;

  if ( $GLOBALS ['padInfoXref']  )

    foreach ( $padParms [$pad] as $padEventsOption ) {

      extract ( $padEventsOption );

      if ( $padPrmKind == 'option' )  {

        if ( file_exists ( "options/$padPrmName.php" ) )
          padInfoXref ( 'options', 'general', $padPrmName );
        
        padInfoXref ( 'options', 'all', $padPrmName );

      }

    }

?>