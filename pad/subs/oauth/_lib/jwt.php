<?php

  // JSON Web Tokens and the P-256 keys behind them, for the subsystems that talk to the outside
  // world: the client secret Apple asks for (an ES256 JWT, subs/oauth/types/apple.php), the
  // VAPID token of a web push (subs/channels/types/webpush.php) and the claims of an OpenID
  // Connect id_token (subs/oauth/_lib/oauth.php). openssl does the curve; base64url is
  // padCryptB64 (lib/crypt.php).
  //
  // padJwtSign      header.claims.signature, signed ES256 with a P-256 private key (PEM)
  // padJwtVerify    whether the ES256 signature of such a token holds for a public key (PEM)
  // padJwtClaims    the claims of a token, read and not checked - the caller checks what it
  //                 relies on; NULL for text that is no JWT
  // padEcPem        the PEM of a P-256 private key from its raw 32-byte scalar and 65-byte point
  // padEcPublicPem  the PEM of a P-256 public key from its raw 65-byte point
  // padEcRaw        the raw scalar and point of a P-256 key (PEM or key) - [ d, point ]
  // padEcNew        a new P-256 key pair, raw - [ d, point ]
  // padEcDerToRaw   an ECDSA signature as openssl writes it (DER) as a JWS wants it, r || s
  // padEcRawToDer   and back, for openssl_verify
  //
  // An ECDSA signature takes a random number of its own, so the same token signed twice is
  // two different signatures: a test verifies a signature, it never compares one.

  const padEcCurveOid = '06082a8648ce3d030107';     // prime256v1
  const padEcKeyOid   = '06072a8648ce3d0201';       // id-ecPublicKey

  function padJwtSign ( $header, $claims, $pem ) {

    $key = is_string ( $pem ) ? openssl_pkey_get_private ( $pem ) : $pem;

    if ( $key === FALSE )
      return '';

    $input = padCryptB64 ( json_encode ( $header, JSON_UNESCAPED_SLASHES ) ) . '.'
           . padCryptB64 ( json_encode ( $claims, JSON_UNESCAPED_SLASHES ) );

    if ( ! openssl_sign ( $input, $der, $key, OPENSSL_ALGO_SHA256 ) )
      return '';

    $raw = padEcDerToRaw ( $der );

    return ( $raw === '' ) ? '' : $input . '.' . padCryptB64 ( $raw );

  }

  function padJwtVerify ( $jwt, $publicPem ) {

    $parts = explode ( '.', (string) $jwt );

    if ( count ( $parts ) != 3 )
      return FALSE;

    $raw = padCryptUnB64 ( $parts [2] );

    if ( $raw === NULL or strlen ( $raw ) != 64 )
      return FALSE;

    return openssl_verify ( $parts [0] . '.' . $parts [1], padEcRawToDer ( $raw ), $publicPem, OPENSSL_ALGO_SHA256 ) === 1;

  }

  function padJwtClaims ( $jwt ) {

    $parts = explode ( '.', (string) $jwt );

    if ( count ( $parts ) != 3 )
      return NULL;

    $json   = padCryptUnB64 ( $parts [1] );
    $claims = ( $json === NULL ) ? NULL : json_decode ( $json, TRUE );

    return is_array ( $claims ) ? $claims : NULL;

  }

  // SEC1's ECPrivateKey (RFC 5915) with the curve and the public point, as openssl reads it.

  function padEcPem ( $d, $point ) {

    if ( strlen ( $d ) != 32 or strlen ( $point ) != 65 or $point [0] != "\x04" )
      return '';

    $der = hex2bin ( '30770201010420' ) . $d . hex2bin ( 'a00a' . padEcCurveOid . 'a144034200' ) . $point;

    return "-----BEGIN EC PRIVATE KEY-----\n" . chunk_split ( base64_encode ( $der ), 64, "\n" ) . "-----END EC PRIVATE KEY-----\n";

  }

  // SubjectPublicKeyInfo of an uncompressed P-256 point.

  function padEcPublicPem ( $point ) {

    if ( strlen ( $point ) != 65 or $point [0] != "\x04" )
      return '';

    $der = hex2bin ( '3059' . '3013' . padEcKeyOid . padEcCurveOid . '034200' ) . $point;

    return "-----BEGIN PUBLIC KEY-----\n" . chunk_split ( base64_encode ( $der ), 64, "\n" ) . "-----END PUBLIC KEY-----\n";

  }

  function padEcRaw ( $key ) {

    if ( is_string ( $key ) )
      $key = openssl_pkey_get_private ( $key );

    $details = ( $key === FALSE ) ? FALSE : openssl_pkey_get_details ( $key );

    if ( ! is_array ( $details ) or ( $details ['ec'] ['curve_name'] ?? '' ) !== 'prime256v1' )
      return [ '', '' ];

    $pad = fn ( $bytes ) => str_pad ( (string) $bytes, 32, "\0", STR_PAD_LEFT );

    return [ $pad ( $details ['ec'] ['d'] ?? '' ), "\x04" . $pad ( $details ['ec'] ['x'] ) . $pad ( $details ['ec'] ['y'] ) ];

  }

  function padEcNew () {

    $key = openssl_pkey_new ( [ 'curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC ] );

    return ( $key === FALSE ) ? [ '', '' ] : padEcRaw ( $key );

  }

  // SEQUENCE { INTEGER r, INTEGER s } - each integer without the sign byte openssl puts in
  // front of a high bit, and left-padded to the curve's 32 bytes.

  function padEcDerToRaw ( $der ) {

    $at  = 0;
    $len = strlen ( $der );

    if ( $len < 8 or ord ( $der [$at++] ) != 0x30 )
      return '';

    $seq = ord ( $der [$at++] );

    if ( $seq & 0x80 )
      $at += $seq & 0x7f;

    $out = '';

    for ( $i = 0; $i < 2; $i++ ) {

      if ( $at + 2 > $len or ord ( $der [$at++] ) != 0x02 )
        return '';

      $size = ord ( $der [$at++] );
      $int  = ltrim ( substr ( $der, $at, $size ), "\0" );
      $at  += $size;

      if ( strlen ( $int ) > 32 )
        return '';

      $out .= str_pad ( $int, 32, "\0", STR_PAD_LEFT );

    }

    return $out;

  }

  function padEcRawToDer ( $raw ) {

    $int = function ( $bytes ) {
      $bytes = ltrim ( $bytes, "\0" );
      if ( $bytes === '' or ord ( $bytes [0] ) & 0x80 )
        $bytes = "\0$bytes";
      return "\x02" . chr ( strlen ( $bytes ) ) . $bytes;
    };

    $body = $int ( substr ( $raw, 0, 32 ) ) . $int ( substr ( $raw, 32, 32 ) );

    return "\x30" . chr ( strlen ( $body ) ) . $body;

  }

?>
