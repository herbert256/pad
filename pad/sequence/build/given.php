<?php

  // Applies the build= parameter, which overrides the strategy pqBuild() inferred.
  //
  // First step of build/build.php; does nothing unless build= named a strategy. When the
  // main sequence is a stored one (pull/fixed/build/given) there is nothing to generate,
  // so the override is handed to the first play instead of $pqBuild.

  if ( ! $pqBuildName or $pqBuildName === TRUE )
    return;

  if ( pqStore ( $pqBuild ) ) {

    foreach ( $pqPlays as $padK => $padV ) {
      $pqPlays [$padK] ['pqBuild'] = $pqBuildName;
      return;
    }

  } else

    $pqBuild = $pqBuildName;

?>