<?php

  // Reads the tag's options and turns the play-related ones into $pqPlays.
  //
  // Runs from every sequence entry point, before the build. Walks the tag's start options
  // in written order, skipping anything already consumed ($pqDone) or not an option. A
  // make/keep/remove/flag option carrying a value registers a play there and then, the
  // value being split on '|' into sequence name and parameter; carrying no value it only
  // sets $pqPlay, the kind that the sequence-named options after it inherit. Any other
  // option naming a real sequence type is registered as a play of the current kind.

  foreach ( $padParms [$pad] as $padStartOption ) {

    extract ( $padStartOption );

        if ( in_array ( $padPrmName, $pqDone ) ) continue;
    elseif ( $padPrmKind != 'option'           ) continue;

    if ( pqPlay ( $padPrmName ) and $padPrmValue and $padPrmValue !== TRUE ) {
      $pqPlay = $padPrmName;
      padSplit ( '|', $padPrmValue, $padPrmName, $padPrmValue );
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