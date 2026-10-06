<?php

  // A dotted key of a JSON body is a path into the request's values for padRequest - its
  // whole name is looked for first - so {"user.id":43} sent to the link signed for
  // user[id]=42 was read as user.id 43 under a valid signature: a body key whose part before
  // its first dot is a value of the link is refused like the value itself. A dotted key of
  // the body's own - note.text - leaves the signature valid.

  $answers = [];
  $link    = padSignedUrl ( 'helpers/signing_target', [ 'user' => [ 'id' => 42 ] ] );

  foreach ( [ 'as made' => NULL, 'user.id' => '{"user.id":43}', 'user.role' => '{"user.role":"admin"}', 'note.text' => '{"note.text":"hi"}' ] as $what => $body ) {

    $got = padCurl ( $body === NULL ? $link : [ 'url' => $link, 'post' => $body, 'headers' => [ 'Content-Type' => 'application/json' ] ] );

    preg_match ( '/\b(in)?valid\b/', (string) $got ['data'], $match );

    $answers [] = "$what: " . ( $match [0] ?? 'nothing' ) . ' ' . $got ['result'];

  }

  $answers = implode ( "\n", $answers );

?>
