<?php

  global $padInfoXref;

  if ( $padTagSeq [$pad] )
    return;

  if ( $padInfoXref  )

    foreach ( $padParms [$pad] as $padEventsOption ) {

      extract ( $padEventsOption );

      if ( $padPrmKind == 'option' )  {

        if ( file_exists ( PAD . "options/$padPrmName.php" ) )
          padInfoXref ( 'options', 'general', $padPrmName );

        padInfoXref ( 'options', 'all', $padPrmName );

      }

    }

?>