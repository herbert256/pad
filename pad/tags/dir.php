<?php

  // {dir '/path'} lists a directory as this level's data, one occurrence per entry, with
  // . and .. left out by padFiles(). The path is used as given; {files} is the version
  // with a base, masks, recursion and a field per entry.

  $padDir = $padParm;

  return padFiles ($padDir);

?>