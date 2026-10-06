<?php

  // $padAppKey, when set, is the key: 32 bytes, or 'base64:' and 32 bytes in base64 - the
  // same key either way. It is read when a value is sealed or opened, so it wins over the
  // key file from then on.

  $padAppKey = str_repeat ( 'k', 32 );

  $raw = padEncrypt ( 'sealed with the raw form' );

  $padAppKey = 'base64:' . base64_encode ( str_repeat ( 'k', 32 ) );

  $keyForms = padDecrypt ( $raw, 'refused' ) . ' / ' . padDecrypt ( padEncrypt ( [ 1, 2 ] ) ) [1];

  $padAppKey = '';

  $keyForms .= ' / ' . padDecrypt ( $raw, 'the key file is another key' );

?>
