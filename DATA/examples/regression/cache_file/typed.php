<?php

  // A page that chose its own content type is not answered from the cache: a hit sends the
  // configured type, so the second fetch of a JSON page went out as text/html. Fetched
  // twice, it must be JSON both times.

  $typedOne = padCurl ( $padHost . 'regression/cache_file/?json&padInclude&typed' );
  $typedTwo = padCurl ( $padHost . 'regression/cache_file/?json&padInclude&typed' );

  $typedResult = 'first: '   . ( $typedOne ['headers'] ['Content-Type'] ?? '' )
               . ', second: ' . ( $typedTwo ['headers'] ['Content-Type'] ?? '' );

?>
