<?php

  // A {push} inside a {nocache} part of a page the cache would keep pushes what this fetch
  // renders: nocachepush, fetched twice at a fresh address, prints a new part and a new push
  // both times. The page was kept with its stack filled in - the part rendered anew on every
  // hit, its push showed the first fetch's text for the entry's whole life.

  $pushUrl = $padHost . 'regression/cache_file/?nocachepush&padInclude&fresh=' . bin2hex ( random_bytes ( 6 ) );

  preg_match ( '/<b>(\w+)<\/b>.*\[(\w+)\]/s', padCurl ( $pushUrl ) ['data'], $pushOne );
  preg_match ( '/<b>(\w+)<\/b>.*\[(\w+)\]/s', padCurl ( $pushUrl ) ['data'], $pushTwo );

  $pushResult = 'part new: '   . ( ( $pushOne [2] ?? 1 ) !== ( $pushTwo [2] ?? 1 ) ? 'yes' : 'no' )
              . ', push new: ' . ( ( $pushOne [1] ?? 1 ) !== ( $pushTwo [1] ?? 1 ) ? 'yes' : 'no' );

?>
