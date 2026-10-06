<?php

  // A signed link's values cannot be named again in the body of the request: the page's
  // variables take a post over the query string, and padRequest a JSON body over both, so
  // a post of id=43 to the link signed for id=42 was read as 43 under a valid signature.
  // A field of the body's own - a form posted to the link - is no value of the link and
  // leaves the signature valid.

  $target  = 'helpers/signing_target';
  $answers = [];

  $fetch = function ( $what, $input ) use ( &$answers ) {
    $got = padCurl ( $input );
    preg_match ( '/\b(in)?valid\b/', (string) $got ['data'], $match );
    $answers [] = "$what: " . ( $match [0] ?? 'nothing' ) . ' ' . $got ['result'];
  };

  $link = padSignedUrl ( $target, [ 'id' => 42 ] );

  $fetch ( 'as made',        $link );
  $fetch ( 'posted again',   [ 'url' => $link, 'post' => [ 'id' => '43' ] ] );
  $fetch ( 'in a json body', [ 'url' => $link, 'post' => '{"id":43}', 'headers' => [ 'Content-Type' => 'application/json' ] ] );
  $fetch ( 'a field added',  [ 'url' => $link, 'post' => [ 'confirm' => 'yes' ] ] );

  $answers = implode ( "\n", $answers );

?>
