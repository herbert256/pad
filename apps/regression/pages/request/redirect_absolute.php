<?php

  // {redirect} to an absolute address goes there, as TAGS.md says - "URL to redirect to" -
  // and as the strict check has it, which lets a URL-shaped target leave the application
  // unchecked: it went through padRedirect as a page name and the browser was sent to
  // ?https://example.com/landing on this site. The values set on the tag ride along.

  $raAway = padCurl ( [ 'url' => $padGoExt . 'request/hopaway&padInclude', 'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );

  $raResult = $raAway ['result'] . ' ' . ( $raAway ['headers'] ['Location'] ?? '' );

?>
