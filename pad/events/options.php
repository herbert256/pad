<?php

  // Fires from events/levelEnd.php as a level closes and walks the tag's parsed parameters,
  // recording every option in the xref report - under 'general' when a pad/options/<name>.php
  // exists, and always under 'all'.
  //
  // Sequence tags are skipped: their options are reported by the sequence subsystem through
  // events/sequence.php instead.

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