<?php

  // Classifies one tag option for the xref report: 'general' when a pad/options/<name>.php
  // handles it, otherwise 'specific' to the tag. Names that are really sequence actions (PA),
  // sequence types (PT) or stored sequences are skipped.
  //
  // Nothing includes this file at present; events/options.php does the equivalent once per
  // level from events/levelEnd.php.

  global $padInfoXref;

  if ( $padInfoXref  )
    if ( ! file_exists ( PA . "$padPrmName.php" ) )
      if ( ! file_exists ( PT . "$padPrmName" ) )
        if ( ! isset  ( $pqStore [$padPrmName] ) )
          if ( file_exists ( PAD . "options/$padPrmName.php" ) )
            padInfoXref ( 'options', 'general', $padPrmName );
          else
            padInfoXref ( 'options', 'specific', $padPrmName );

?>