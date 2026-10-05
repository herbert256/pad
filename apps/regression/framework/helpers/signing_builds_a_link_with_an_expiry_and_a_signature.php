<?php

  // padSignedUrl is a link to a page of this application, absolute, with padExpires (a
  // Unix time) when it expires and padSignature - 64 hex characters - last. The lifetime is
  // seconds, a DateInterval or a moment; values may be written in the page name; '' is
  // this page; $padCleanUrls gives the clean form. The host and the signature are shown as
  // HOST and SIG here: both differ per machine.

  $padTimezone = 'UTC';

  padNowFreeze ( '2026-01-15 10:00:00' );

  $show = fn ( $url ) => preg_replace ( '/padSignature=[0-9a-f]{64}$/', 'padSignature=SIG', str_replace ( $padHost, 'HOST/', $url ) );

  $links = [
    $show ( padSignedUrl ( 'invoice', [ 'id' => 42 ] ) ),
    $show ( padSignedUrl ( '?orders/show&id=3', [ 'tab' => 'lines items', 'all' => TRUE, 'ids' => [ 4, 5 ] ], 3600 ) ),
    $show ( padSignedUrl ( 'invoice', [ 'id' => 42 ], new DateInterval ( 'PT1H' ) ) ),
    $show ( padSignedUrl ( 'invoice', [ 'id' => 42 ], '2026-01-15 11:00:00' ) ),
    $show ( padSignedUrl ( '', NULL ) ),
  ];

  $padCleanUrls = TRUE;

  $links [] = $show ( padSignedUrl ( 'invoice', [ 'id' => 42 ], 60 ) );

  $padCleanUrls = FALSE;

  $links = implode ( ' | ', $links );

  $same = ( padSignedUrl ( 'invoice', [ 'a' => 1, 'b' => 2 ] ) === str_replace ( 'b=2&a=1', 'a=1&b=2', padSignedUrl ( 'invoice', [ 'b' => 2, 'a' => 1 ] ) ) )
        ? 'the order of the values does not count' : 'the order counts';

  $here = padSignatureValid () ? 'signed' : 'not signed';

?>
