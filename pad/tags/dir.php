<?php

  // {dir '/path'} lists a directory as this level's data, one occurrence per entry, with
  // . and .. left out by padFiles(). The path must lie inside the applications, the engine
  // or DATA (padDirContained); {files} is the version with a base, masks, recursion and a
  // field per entry.

  $padDir = $padParm;

  // A directory that is not there was a raw scandir failure. Strict mode names it; the
  // lenient walk answers the empty list a scan of nothing is.

  if ( ! is_dir ( $padDir ) ) {

    if ( $padCheckSyntax )
      padError ( "there is no directory named '$padDir' for {dir}" );

    return [];

  }

  if ( ! padDirContained ( $padDir ) ) {
    padError ( "the directory '$padDir' lies outside the applications, the engine and DATA" );
    return [];
  }

  return padFiles ($padDir);

?>