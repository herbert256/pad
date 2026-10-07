<?php

  // An address list is split by walking it: a quote with no partner is a character of the
  // name - "Ann "Ace Lee" <ann@example.com> is one address, as it was before the split
  // learned quoted names - and a quoted name of any length is read whole. The split was one
  // regular expression: the stray quote cut the address and stopped the mail, and a name of
  // ten thousand characters ran out of PCRE's JIT stack, which lost every address of the
  // list - bob's too - and the mail had nobody to go to.

  padMail ( 'carol@example.com', '', 'Hello', [], [ 'html' => '<p>Hello</p>', 'replyTo' => '"Ann "Ace Lee" <ann@example.com>' ] );

  echo $padMailLast ['replyTo'], "\n";

  unlink ( $padMailLast ['file'] );

  padMail ( '"' . str_repeat ( 'a', 12000 ) . '" <ann@example.com>, bob@example.com', '', 'Hello', [], [ 'html' => '<p>Hello</p>' ] );

  $longTo = explode ( ', ', $padMailLast ['to'] );

  echo count ( $longTo ), ' addresses: a name of ', strlen ( $longTo [0] ) - strlen ( ' <ann@example.com>' ) - 2, ' characters <ann@example.com>, ', $longTo [1] ?? 'nobody';

  unlink ( $padMailLast ['file'] );

?>
