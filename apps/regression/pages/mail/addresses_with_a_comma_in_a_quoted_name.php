<?php

  // An address list is split on the commas between its addresses, not on one inside a
  // quoted name - "Doe, John" <john@example.com> is one address, the way RFC 5322 and
  // every mail client write a name that holds a comma. It was split there too, the half
  // "Doe was no address, and the mail stopped on that error.

  padMail ( '"Doe, John" <john@example.com>, carol@example.com', '', 'Hello', [],
            [ 'html' => '<p>Hello</p>', 'replyTo' => '"Smith, Ann" <ann@example.com>' ] );

  echo $padMailLast ['to'], ' | ', $padMailLast ['replyTo'];

  unlink ( $padMailLast ['file'] );

?>
