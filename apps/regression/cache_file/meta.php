<?php

  // {meta cache=0} keeps a page out of the cache the application has on: two fetches of
  // metaoff - a body that is its build moment, a number - answer with two numbers.

  $metaOne = trim ( padCurl ( $padHost . 'regression/cache_file/?metaoff&padInclude' ) ['data'] ?? '' );
  $metaTwo = trim ( padCurl ( $padHost . 'regression/cache_file/?metaoff&padInclude' ) ['data'] ?? '' );

  $metaBuilt  = ( ctype_digit ( $metaOne ) and ctype_digit ( $metaTwo ) and $metaOne !== $metaTwo );
  $metaResult = 'meta cache=0 builds every time: ' . ( $metaBuilt ? 'yes' : 'NO' );

?>
