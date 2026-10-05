<?php

  // padUrl: a page of this application with values as a query - arrays too, NULLs left
  // out - a #fragment at the end, a page written as a link writes it (?about, about&x=1),
  // the current page when none is named, and the clean form when $padCleanUrls is on at the
  // moment of the call. The absolute form starts with $padHost.

  $u1 = padUrl ( 'products/42' );
  $u2 = padUrl ( 'orders', [ 'sort' => 'date', 'page' => 2, 'skip' => NULL ] );
  $u3 = padUrl ( 'search', [ 'q' => 'red & blue', 'tags' => [ 'new', 'sale' ] ] );
  $u4 = padUrl ( '?about&x=1#team', [ 'y' => 2 ] );
  $u5 = padUrl ( 'blog/hello world' );
  $u6 = padUrl ();
  $u7 = padUrl ( 'orders', 'sort=date' );

  $padAbsolute = padUrl ( 'products/42', [], TRUE );
  $u8 = str_starts_with ( $padAbsolute, $padHost ) ? 'on $padHost: ' . substr ( $padAbsolute, strlen ( $padHost ) ) : $padAbsolute;

  $padCleanUrls = TRUE;

  $c1 = padUrl ( 'products/42' );
  $c2 = padUrl ( 'orders', [ 'sort' => 'date' ] );
  $c3 = padUrl ( 'about&x=1', [ 'y' => 2 ] );

  $padCleanUrls = FALSE;

?>
