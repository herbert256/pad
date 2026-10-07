<?php

  // A secret in a query - ?reset&token=..., the password of a form sent by GET - was
  // redacted where the request values show it, $_GET and $_REQUEST, and stood in clear a
  // few lines on in REQUEST_URI and QUERY_STRING, in the referer of the next page, in the
  // dumps, the JSON channel and the track files.

  $redacted = json_encode ( padRedact ( [
    'REQUEST_URI'  => '/pad/shop/?reset&token=zzQueryTok&password=zzQueryPass&page=2',
    'QUERY_STRING' => 'token=zzQueryTok&page=2',
    'HTTP_REFERER' => 'http://example.com/a?b=1&api_key=k1#top',
    'text'         => 'the page=2 of 3'
  ] ), JSON_UNESCAPED_SLASHES );

?>
