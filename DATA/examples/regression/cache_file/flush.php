<?php

  // A flushed page goes without an ETag - {flush} gives the ETag up with the whole body it
  // describes - where the headers that went out with the first part carried Etag: "", one
  // tag for every flushed page of the application.

  $flushAnswer = padCurl ( $padHost . 'regression/cache_file/?flushed&padInclude&flush=' . padRandomString () );

  $flushResult = 'body: ' . trim ( $flushAnswer ['data'] ) . ', etag: '
               . ( array_key_exists ( 'Etag', $flushAnswer ['headers'] ) || array_key_exists ( 'ETag', $flushAnswer ['headers'] ) ? 'sent' : 'none' );

?>
