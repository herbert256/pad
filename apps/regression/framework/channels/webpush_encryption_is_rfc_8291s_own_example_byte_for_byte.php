<?php

  // The web push encryption against RFC 8291's own example (section 5 and appendix A): the
  // user agent's public key and auth secret, the application server's key pair and the salt
  // of the example give the example's body - header and ciphertext - byte for byte; the user
  // agent's private key opens it again. openssl's ECDH, hash_hkdf and AES-128-GCM.

  include_once PAD . 'subs/channels/_lib/webpush.php';

  $b64 = fn ( $text ) => padCryptUnB64 ( $text );

  $uaPublic  = $b64 ( 'BCVxsr7N_eNgVRqvHtD0zTZsEc6-VV-JvLexhqUzORcxaOzi6-AYWXvTBHm4bjyPjs7Vd8pZGH6SRpkNtoIAiw4' );
  $uaPrivate = $b64 ( 'q1dXpw3UpT5VOmu_cf_v6ih07Aems3njxI-JWgLcM94' );
  $auth      = $b64 ( 'BTBZMqHH6r4Tts7J_aSIgg' );
  $asPrivate = $b64 ( 'yfWPiYE-n46HLnH0KqZOF1fJJU3MYrct3AELtAQ-oRw' );
  $asPublic  = $b64 ( 'BP4z9KsN6nGRTbVYI_c7VJSPQTBtkgcy27mlmlMoZIIgDll6e3vCYLocInmYWAmS6TlzAC8wEqKK6PBru3jl7A8' );
  $salt      = $b64 ( 'DGv6ra1nlYgDCS1FRnbzlw' );

  $body = padWebPushEncrypt ( 'When I grow up, I want to be a watermelon', $uaPublic, $auth, $salt, [ $asPrivate, $asPublic ] );

  $rfc  = 'DGv6ra1nlYgDCS1FRnbzlwAAEABBBP4z9KsN6nGRTbVYI_c7VJSPQTBtkgcy27ml'
        . 'mlMoZIIgDll6e3vCYLocInmYWAmS6TlzAC8wEqKK6PBru3jl7A_yl95bQpu6cVPT'
        . 'pK4Mqgkf1CXztLVBSt2Ks3oZwbuwXPXLWyouBWLVWGNWQexSgSxsj_Qulcy4a-fN';

  $same   = padCryptB64 ( $body ) === $rfc ? 'the same bytes' : 'other bytes: ' . padCryptB64 ( $body );
  $opened = padWebPushDecrypt ( $b64 ( $rfc ), $uaPrivate, $uaPublic, $auth );
  $size   = strlen ( $body );

?>
