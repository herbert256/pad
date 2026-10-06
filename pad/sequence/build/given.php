<?php

  // Applies the build= parameter, which overrides the strategy pqBuild() inferred.
  //
  // First step of build/build.php; does nothing unless build= named a strategy. When the
  // main sequence is a stored one (pull/fixed/build/given) there is nothing to generate,
  // so the override is handed to the first play instead of $pqBuild.

  if ( ! $pqBuildName or $pqBuildName === TRUE )
    return;

  // The name becomes part of an include path - build/types/<name>.php for the main
  // sequence, plays/play/<name>.php for a play - so it must name a strategy there is a file
  // for, and nothing else: with ../ in it, build= reached any .php on disk. Any other name
  // is an error, and the strategy pqBuild() inferred stays.

  $pqBuildDir = pqStore ( $pqBuild ) ? 'plays/play/' : 'build/types/';

  if ( ! padValidName ( $pqBuildName ) or ! file_exists ( PQ . "$pqBuildDir$pqBuildName.php" ) ) {
    padError ( "there is no build strategy named '$pqBuildName'" );
    return;
  }

  // And the sequence it is put on has to have what the strategy runs - its own loop.php,
  // order.php, fixed.php and so on. build='order' on prime included a types/prime/order.php
  // that is not there and ended the request on the include warning, as fixed and build did on
  // the main sequence and on a play, and given looked for the list only an action's call
  // hands over. check needs no file of the type's, nor does pull for the main sequence or
  // order for a play, which reads the type's table. A store with no play to put it on leaves
  // the name with nothing to do, as before.

  $pqBuildSeq  = pqStore ( $pqBuild ) ? ( reset ( $pqPlays ) ['pqSeq'] ?? '' ) : $pqSeq;
  $pqBuildFree = pqStore ( $pqBuild ) ? [ 'check', 'order' ] : [ 'check', 'pull' ];

  if ( $pqBuildSeq and ! in_array ( $pqBuildName, $pqBuildFree )
                   and ! file_exists ( PT . "$pqBuildSeq/$pqBuildName.php" ) ) {
    padError ( "the $pqBuildSeq sequence has no build strategy named '$pqBuildName'" );
    return;
  }

  if ( pqStore ( $pqBuild ) ) {

    foreach ( $pqPlays as $padK => $padV ) {
      $pqPlays [$padK] ['pqBuild'] = $pqBuildName;
      return;
    }

  } else

    $pqBuild = $pqBuildName;

?>
