<?php

  if ( $padTagSeq [$pad] )
    return;

  if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] )

    foreach ( $padParms [$pad] as $padEventsOption ) {

      extract ( $padEventsOption );

      if ( $padPrmKind == 'option' )  {

        if ( file_exists ( "options/$padPrmName.php" ) )
          padInfoXapp ( 'options', 'general', $padPrmName );
        
        padInfoXapp ( 'options', 'all', $padPrmName );

      }

    }

?>