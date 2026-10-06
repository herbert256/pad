<?php

  // Reads the tag's options and turns the play-related ones into $pqPlays.
  //
  // Runs from every sequence entry point, before the build. Walks the tag's start options
  // in written order, skipping anything already consumed ($pqDone) or not an option. A
  // make/keep/remove/flag option carrying a value registers a play there and then, the
  // value being split on '|' into sequence name and parameter; carrying no value it only
  // sets $pqPlay, the kind that the sequence-named options after it inherit. Any other
  // option naming a real sequence type is registered as a play of the current kind.

  // Only a tag's run has options to read. A run started from an expression has its call's
  // arguments and nothing else, and reading the level here gave it the plays of the tag it
  // was evaluated inside: {sequence 5, make, eval='sequence:square(@)'} re-ran its own eval
  // in every inner run, until the stack gave out.

  foreach ( ( $pqEntry == 'tag' ) ? $padParms [$pad] : [] as $padStartOption ) {

    extract ( $padStartOption );

        if ( in_array ( $padPrmName, $pqDone ) ) continue;
    elseif ( $padPrmKind != 'option'           ) continue;

    if ( pqPlay ( $padPrmName ) and $padPrmValue and $padPrmValue !== TRUE ) {
      $pqPlay = $padPrmName;
      padSplit ( '|', $padPrmValue, $padPrmName, $padPrmValue );

      // The name in the value has to be a sequence type, as an option's name has to be: it
      // goes into the include paths of the type's files, and unchecked make='../../x' reached
      // an init.php, bool.php or function.php anywhere on disk - a template writing
      // make=$value handed that to its visitor - while a name that was no type at all ended
      // the request on the missing plays/play/unknown.php.

      if ( ! pqSeq ( $padPrmName ) ) {
        padError ( "$pqPlay='$padPrmName' is not a sequence type" );
        continue;
      }

      include PQ . 'plays/add.php';
      continue;
    }

    if ( pqPlay ( $padPrmName ) ) {
      $pqPlay = $padPrmName;
      continue;
    }

    if ( ! pqSeq ( $padPrmName ) )
      continue;

    include PQ . 'plays/add.php';

  }

?>
