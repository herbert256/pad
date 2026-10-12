<?php

  // A tab set kept in a {cache} section and one with the same tabs rendered next to it
  // share no id, also when the section comes from the cache: the kept set's id is made
  // with the section's key, where both were made from the tab names alone and on a hit
  // the two sets had one id and one radio group - choosing a tab in one emptied the other.
  // widgetcached is fetched twice: the second answer has the section from the cache.

  $url = $padHost . 'regression/cache_file/?widgetcached&padInclude';

  padCurl ( $url );

  $hit = padCurl ( $url ) ['data'] ?? '';

  preg_match_all ( '/ id="([^"]+)"/', $hit, $found );

  $widgetIds    = count ( $found [1] );
  $widgetShared = $widgetIds - count ( array_unique ( $found [1] ) );

?>
