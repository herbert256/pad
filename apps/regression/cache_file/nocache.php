<?php

  // Fetches nocacheprobe twice at an address new on every run: the second fetch is a hit
  // of the page cache, so its build moment is the first fetch's, while its {nocache} parts
  // render again - the time differs - and the part inside the loop still has its row.

  $url = $padHost . 'regression/cache_file/?nocacheprobe&padInclude&run=' . padRandomString ();

  $one = trim ( padCurl ( $url ) ['data'] ?? '' );
  $two = trim ( padCurl ( $url ) ['data'] ?? '' );

  $pattern = '/^built (\d+) live (\S+ \d+) rows 1=1;2=2;$/';

  $okOne = preg_match ( $pattern, $one, $partsOne );
  $okTwo = preg_match ( $pattern, $two, $partsTwo );

  $cached = ( $okOne and $okTwo and $partsOne [1] === $partsTwo [1] ) ? 'yes' : 'NO';
  $live   = ( $okOne and $okTwo and $partsOne [2] !== $partsTwo [2] ) ? 'yes' : 'NO';

?>
