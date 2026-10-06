<?php

  // {dir '/path'} lists a directory as this level's data, one occurrence per entry, with
  // . and .. left out by padFiles(). The path must lie inside the applications, the engine
  // or DATA (padDirContained); {files} is the version with a base, masks, recursion and a
  // field per entry.
  //
  // The path is kept in a name of its own: it was kept in $padDir, the directory of the
  // page, which every later _tags/, _include/ and _lang/ lookup of the request walks up from
  // (padDirs), so after a {dir} the page no longer found what its own directory holds.

  $padDirScan = $padParm;

  // A directory that is not there was a raw scandir failure. Strict mode names it; the
  // lenient walk answers the empty list a scan of nothing is.

  if ( ! is_dir ( $padDirScan ) ) {

    if ( $padCheckSyntax )
      padError ( "there is no directory named '$padDirScan' for {dir}" );

    return [];

  }

  if ( ! padDirContained ( $padDirScan ) ) {
    padError ( "the directory '$padDirScan' lies outside the applications, the engine and DATA" );
    return [];
  }

  return padFiles ($padDirScan);

?>
