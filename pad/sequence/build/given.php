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

  if ( pqStore ( $pqBuild ) ) {

    foreach ( $pqPlays as $padK => $padV ) {
      $pqPlays [$padK] ['pqBuild'] = $pqBuildName;
      return;
    }

  } else

    $pqBuild = $pqBuildName;

?>