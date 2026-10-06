<?php

  // padSignatureValid on real requests: a signed link to signing_target is fetched as it
  // was made, in the clean form, with its values in another order - all valid - and with a
  // value changed, added or removed, the signature left out, an expiry passed or moved,
  // and the signature of another page - all invalid.

  $target  = 'helpers/signing_target';
  $answers = [];

  $fetch = function ( $what, $url ) use ( &$answers ) {
    $got = padCurl ( $url );
    preg_match ( '/\b(in)?valid\b/', (string) $got ['data'], $match );
    $answers [] = "$what: " . ( $match [0] ?? 'nothing' ) . ' ' . $got ['result'];
  };

  $link = padSignedUrl ( $target, [ 'id' => 42, 'all' => TRUE, 'ids' => [ 4, 5 ], 'note' => ' a b ' ] );

  $fetch ( 'as made',   $link );
  $fetch ( 'reordered', str_replace ( 'id=42&all=1', 'all=1&id=42', $link ) );
  $fetch ( 'changed',   str_replace ( 'id=42', 'id=43', $link ) );
  $fetch ( 'added',     str_replace ( '&padSignature', '&admin=1&padSignature', $link ) );
  $fetch ( 'removed',   str_replace ( 'all=1&', '', $link ) );
  $fetch ( 'unsigned',  preg_replace ( '/&padSignature=.*$/', '', $link ) );
  $fetch ( 'elsewhere', str_replace ( '?helpers/signing_other', "?$target", padSignedUrl ( 'helpers/signing_other', [ 'id' => 42 ] ) ) );

  // The clean form - the page in the path - and the path with its values after an &,
  // fetched as index.php/<page>, the form every server runs without being told.

  $padCleanUrls = TRUE;
  $clean        = str_replace ( "$padApp/$target", "$padApp/index.php/$target", padSignedUrl ( $target, [ 'id' => 42 ], 600 ) );
  $padCleanUrls = FALSE;

  $fetch ( 'clean',      $clean );
  $fetch ( 'clean tail', str_replace ( "$target?", "$target&", $clean ) );

  // An expiry: made two hours ago for one hour, it has passed; made now, it holds - until
  // the time in it is moved.

  padNowFreeze ( '-2 hours' );
  $expired = padSignedUrl ( $target, [ 'id' => 42 ], 3600 );
  padNowFreeze ();

  $fresh = padSignedUrl ( $target, [ 'id' => 42 ], 3600 );

  $fetch ( 'expired', $expired );
  $fetch ( 'fresh',   $fresh );
  $fetch ( 'moved',   preg_replace_callback ( '/padExpires=(\d+)/', fn ( $m ) => 'padExpires=' . ( $m [1] + 3600 ), $fresh ) );

  $answers = implode ( "\n", $answers );

?>
