<?php

  // Without $padAppKey the key is DATA/keys/<application>.key, made the first time one is
  // needed: 32 random bytes, written as 'base64:...' so it can be copied into $padAppKey,
  // readable by its owner only.

  padEncrypt ( 'make the key' );

  $keyFile  = DATA . "keys/$padApp.key";
  $keyText  = trim ( file_get_contents ( $keyFile ) );
  $keyShape = ( str_starts_with ( $keyText, 'base64:' ) ? 'base64: ' : 'raw ' )
            . strlen ( base64_decode ( substr ( $keyText, 7 ), TRUE ) ) . ' bytes, mode '
            . substr ( sprintf ( '%o', fileperms ( $keyFile ) ), -3 );

  // The key the file holds is the key: set as $padAppKey it opens what the file sealed.

  $sealed    = padEncrypt ( 'same key' );
  $padAppKey = $keyText;
  $keyShape .= ', ' . padDecrypt ( $sealed, 'another key' );

?>
