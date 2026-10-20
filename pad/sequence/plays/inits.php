<?php

  // Reads the tag's options and turns the play-related ones into $pqPlays.
  //
  // Runs from every sequence entry point, before the build. Walks the tag's start options
  // in written order, skipping anything already consumed ($pqDone) or not an option. A
  // make/keep/remove/flag option carrying a value registers a play there and then, the
  // value being split on '|' into sequence name and parameter; carrying no value it only
  // sets $pqPlay, the kind that the sequence-named options after it inherit. Any other
  // option naming a real sequence type is registered as a play of the current kind.
  //
  // A valued play is that one play: the kind it sets for plays/add.php is put back after
  // it, where it stayed, and the options after it played as it did - in {sequence '1..10',
  // keep='even', multiply=3} multiply=3 kept the multiples of 3 and answered 6, where
  // written before keep= it is the make play that gives 6 12 18 24 30.

  // Only a tag's run has options to read. A run started from an expression has its call's
  // arguments and nothing else, and reading the level here gave it the plays of the tag it
  // was evaluated inside: {sequence 5, make, eval='sequence:square(@)'} re-ran its own eval
  // in every inner run, until the stack gave out.

  foreach ( ( $pqEntry == 'tag' ) ? $padParms [$pad] : [] as $pqPlaysAt => $padStartOption ) {

    extract ( $padStartOption );

        if ( $pqPlaysAt === ( $pqSeqAt ?? NULL ) ) continue;
    elseif ( $padPrmKind != 'option'           ) continue;

    if ( pqPlay ( $padPrmName ) and $padPrmValue and $padPrmValue !== TRUE ) {
      $pqPlaySave = $pqPlay;
      $pqPlay     = $padPrmName;
      padSplit ( '|', $padPrmValue, $padPrmName, $padPrmValue );

      // A type named without a |parameter plays as its bare option does, with TRUE: left at
      // '', make='multiply' did its arithmetic with the empty text and ended the request on
      // "Unsupported operand types: int * string", where {sequence 3, make, multiply} gives 1 2 3.

      if ( $padPrmValue === '' )
        $padPrmValue = TRUE;

      // The name in the value has to be a sequence type, as an option's name has to be: it
      // goes into the include paths of the type's files, and unchecked make='../../x' reached
      // an init.php, bool.php or function.php anywhere on disk - a template writing
      // make=$value handed that to its visitor - while a name that was no type at all ended
      // the request on the missing plays/play/unknown.php.

      if ( ! pqSeq ( $padPrmName ) ) {
        padError ( "$pqPlay='$padPrmName' is not a sequence type" );
        $pqPlay = $pqPlaySave;
        continue;
      }

      include PQ . 'plays/add.php';
      $pqPlay = $pqPlaySave;
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
