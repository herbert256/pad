<?php

  // {dir '/path'} lists a directory as this level's data, one occurrence per entry, with
  // . and .. left out by padFiles(). The path is used as given; {files} is the version
  // with a base, masks, recursion and a field per entry.

  $padDir = $padParm;

  // A directory that is not there was a raw scandir failure. Strict mode names it; the
  // lenient walk answers the empty list a scan of nothing is.

  if ( ! is_dir ( $padDir ) ) {

    if ( $padCheckSyntax )
      return padError ( "there is no directory named '$padDir' for {dir}" );

    return [];

  }

  return padFiles ($padDir);

?>