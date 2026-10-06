<?php

  // Passwords, sealed values and signed links for a page's PHP - what Laravel's Hash,
  // encrypt()/decrypt() and URL::signedRoute() give, as plain functions on PHP's own
  // password_hash, sodium and hash_hmac.
  //
  // padHash             a password hashed with password_hash and PASSWORD_DEFAULT, for the
  //                     database; never the password itself
  // padHashCheck        whether a password matches a hash padHash made
  // padHashNeedsRehash  whether a hash was made with older settings than PHP's default now
  // padEncrypt          a value JSON-encoded and sealed with sodium_crypto_secretbox and a
  //                     random nonce, answered as one URL-safe string
  // padDecrypt          the value back - or the default for a payload that was tampered
  //                     with, cut short, or sealed with another key
  // padSignedUrl        a link to a page of this application carrying an expiry time and an
  //                     HMAC-SHA256 signature over the address
  // padSignatureValid   whether this request carries a valid, unexpired signature
  //
  // padCryptKey         a 32-byte key for one purpose, derived from the application key
  // padCryptMaster      the application key: $padAppKey, or DATA/keys/<application>.key
  // padCryptKeyText     a key as written - 32 bytes, or 'base64:' and 32 bytes in base64
  // padCryptKeyFile     the key file, made on first use
  // padCryptSignature   the signature of an application, a page and its values
  // padCryptQuery       the values as one query string in a fixed order
  // padCryptB64         bytes as URL-safe base64, and padCryptUnB64 back
  //
  // One application key serves both the sealing and the signing, each through a key of its
  // own derived from it, so a signature never doubles as a sealed value. The key file is
  // made the first time a key is needed - random_bytes, mode 0600, under DATA/ where no URL
  // reaches - so an application encrypts without any setting; $padAppKey, when set, wins,
  // and is the setting for several servers that must read each other's values. Changing the
  // key makes every sealed value and every signed link of before unreadable, by design.

  // What goes into the database: password_hash with PHP's default algorithm and a new salt
  // each time, so one password never gives the same hash twice. A password that is no
  // text - NULL, an array - is the author's mistake and is named, as is one the algorithm
  // refuses (bcrypt: a NUL byte).

  function padHash ( $password ) {

    if ( ! is_string ( $password ) and ! is_int ( $password ) and ! is_float ( $password ) and ! $password instanceof Stringable ) {
      padError ( 'padHash: the password must be text, not ' . get_debug_type ( $password ) );
      return '';
    }

    try {
      return password_hash ( (string) $password, PASSWORD_DEFAULT );
    } catch ( Throwable $e ) {
      padError ( 'padHash: ' . $e->getMessage () );
      return '';
    }

  }

  // A password or a hash that is no text - a missing user's NULL hash, a password[] posted
  // as an array - simply does not match: what a visitor sends is never an error to report.

  function padHashCheck ( $password, $hash ) {

    if ( ! is_string ( $hash ) or $hash === '' )
      return FALSE;

    if ( ! is_string ( $password ) and ! is_int ( $password ) and ! is_float ( $password ) and ! $password instanceof Stringable )
      return FALSE;

    try {
      return password_verify ( (string) $password, $hash );
    } catch ( Throwable $e ) {
      return FALSE;
    }

  }

  // No hash at all, or one that is not a hash, needs one: TRUE.

  function padHashNeedsRehash ( $hash ) {

    if ( ! is_string ( $hash ) or $hash === '' )
      return TRUE;

    return password_needs_rehash ( $hash, PASSWORD_DEFAULT );

  }

  // Anything JSON holds can be sealed - text, numbers, booleans, NULL, arrays; an object
  // comes back as an array. Text that is no UTF-8, INF and NAN are no JSON and are named.

  function padEncrypt ( $value ) {

    if ( ! function_exists ( 'sodium_crypto_secretbox' ) ) {
      padError ( "padEncrypt needs PHP's sodium extension" );
      return '';
    }

    $json = json_encode ( $value, JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

    if ( $json === FALSE ) {
      padError ( 'padEncrypt cannot write the value as JSON: ' . json_last_error_msg () );
      return '';
    }

    $key = padCryptKey ( 'encrypt' );

    if ( $key === NULL )
      return '';

    $nonce = random_bytes ( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

    return padCryptB64 ( $nonce . sodium_crypto_secretbox ( $json, $nonce, $key ) );

  }

  // The payload comes from outside - a cookie, a hidden field, a link - so whatever is
  // wrong with it answers the default and is no error: only the seal says it is ours.

  function padDecrypt ( $payload, $default = NULL ) {

    if ( ! function_exists ( 'sodium_crypto_secretbox_open' ) ) {
      padError ( "padDecrypt needs PHP's sodium extension" );
      return $default;
    }

    if ( ! is_string ( $payload ) )
      return $default;

    $raw = padCryptUnB64 ( $payload );

    if ( $raw === NULL or strlen ( $raw ) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES )
      return $default;

    $key = padCryptKey ( 'encrypt' );

    if ( $key === NULL )
      return $default;

    try {
      $json = sodium_crypto_secretbox_open ( substr ( $raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ),
                                             substr ( $raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ), $key );
    } catch ( Throwable $e ) {
      return $default;
    }

    if ( ! is_string ( $json ) )
      return $default;

    $value = json_decode ( $json, TRUE );

    return ( json_last_error () === JSON_ERROR_NONE ) ? $value : $default;

  }

  // A link like {$padGo}invoice&id=42 - absolute, it is mostly mailed - in the clean form
  // when $padCleanUrls says so, with padExpires (a Unix time) when it expires and
  // padSignature last. $expires is a number of seconds from now, a DateInterval, or a
  // moment padDateParse reads; NULL never expires. Values may also be written in the page
  // name, invoice&id=42; those of $vars win. '' is the page this request runs.

  function padSignedUrl ( $page, $vars = [], $expires = NULL ) {

    global $padApp, $padCleanUrls, $padHost, $padPage, $padPageAsked;

    if ( $vars === NULL )
      $vars = [];

    if ( ! is_array ( $vars ) ) {
      padError ( 'padSignedUrl: the values must be an array, not ' . get_debug_type ( $vars ) );
      return '';
    }

    if ( $page !== NULL and ! is_string ( $page ) and ! $page instanceof Stringable ) {
      padError ( 'padSignedUrl: the page must be a name, not ' . get_debug_type ( $page ) );
      return '';
    }

    $page = ltrim ( trim ( (string) $page ), '?' );

    if ( preg_match ( '/^([^?&]*)[?&](.*)$/s', $page, $match ) ) {
      $page = $match [1];
      parse_str ( $match [2], $more );
      $vars = array_replace ( $more, $vars );
    }

    $page = trim ( padCorrectPath ( $page ), '/' );

    if ( $page === '' )
      $page = trim ( ( $padPageAsked ?? '' ) !== '' ? $padPageAsked : (string) $padPage, '/' );

    foreach ( [ 'padExpires', 'padSignature' ] as $name )
      if ( array_key_exists ( $name, $vars ) ) {
        padError ( "padSignedUrl: $name is the link's own value, not one to give" );
        return '';
      }

    if ( $expires !== NULL ) {

      $now = padNow ();

      if ( is_int ( $expires ) or is_float ( $expires ) or ( is_string ( $expires ) and is_numeric ( trim ( $expires ) ) ) ) {
        if ( (float) $expires <= 0 ) {
          padError ( "padSignedUrl: a link that lives $expires seconds has expired before it is made" );
          return '';
        }
        $until = $now->getTimestamp () + (int) ceil ( min ( (float) $expires, 253402300799 ) );
      } else {
        $moment = ( $expires instanceof DateInterval ) ? $now->add ( $expires ) : padDateParse ( $expires );
        if ( $moment === NULL ) {
          padError ( "padSignedUrl cannot read '" . padMakeSafe ( is_scalar ( $expires ) ? (string) $expires : get_debug_type ( $expires ), 40 ) . "' as the moment the link expires" );
          return '';
        }
        $until = $moment->getTimestamp ();
        if ( $until <= $now->getTimestamp () ) {
          padError ( 'padSignedUrl: the moment the link expires, ' . $moment->format ( 'Y-m-d H:i:s' ) . ', has passed' );
          return '';
        }
      }

      $vars ['padExpires'] = $until;

    }

    $signature = padCryptSignature ( $padApp, $page, $vars );

    if ( $signature === NULL )
      return '';

    $vars ['padSignature'] = $signature;

    // The page's segments are URL-encoded, as padUrl writes them: a route segment with a
    // space - products/Café au lait - stood in the link as it is, and a mail client ends a
    // link at its first space. The signature is over the page as it is asked, decoded.

    $query = http_build_query ( $vars, '', '&', PHP_QUERY_RFC3986 );
    $link  = implode ( '/', array_map ( 'rawurlencode', explode ( '/', $page ) ) );

    return $padHost . "$padApp/" . ( $padCleanUrls ? "$link?" : "?$link&" ) . $query;

  }

  // The request's values as the link had them: every query value but the signature and
  // the bare key that names the page (?invoice), and the page by the name it was asked.
  // An added, removed or changed value, another page, another application, an expiry
  // moved or passed - each is FALSE. The signature is compared in constant time.

  function padSignatureValid () {

    global $padApp, $padPage, $padPageAsked, $padPageKey;

    $given = $_GET ['padSignature'] ?? NULL;

    if ( ! is_string ( $given ) or ! preg_match ( '/^[0-9a-f]{64}$/', $given ) )
      return FALSE;

    $vars = $_GET;

    unset ( $vars ['padSignature'] );

    if ( ( $padPageKey ?? '' ) !== '' and ( $vars [$padPageKey] ?? NULL ) === '' )
      unset ( $vars [$padPageKey] );

    // A value of the link that the body of the request names again would stand in for the
    // signed one: the page's variables take a post over the query string (inits/parms.php)
    // and padRequest a JSON body over both, so a post of id=43 to the link signed for id=42
    // was read as 43 under a valid signature. A field of the body's own - a form posted to
    // the link - is no value of the link.

    $body = padRequestJson () + ( is_array ( $_POST ?? NULL ) ? $_POST : [] );

    foreach ( array_keys ( $_GET ) as $name )
      if ( array_key_exists ( $name, $body ) )
        return FALSE;

    if ( array_key_exists ( 'padExpires', $vars ) ) {

      if ( ! is_string ( $vars ['padExpires'] ) or ! ctype_digit ( $vars ['padExpires'] ) )
        return FALSE;

      if ( padNow ()->getTimestamp () > (int) $vars ['padExpires'] )
        return FALSE;

    }

    $page = trim ( padCorrectPath ( ( $padPageAsked ?? '' ) !== '' ? $padPageAsked : (string) $padPage ), '/' );
    $want = padCryptSignature ( $padApp, $page, $vars );

    return ( $want !== NULL and hash_equals ( $want, $given ) );

  }

  // The signature names the application and the page as well as the values, so a link
  // signed for one page opens no other. Hex, 64 characters: safe in any URL as it is.

  function padCryptSignature ( $app, $page, $vars ) {

    $key = padCryptKey ( 'sign' );

    if ( $key === NULL )
      return NULL;

    return hash_hmac ( 'sha256', "$app\n$page\n" . padCryptQuery ( $vars ), $key );

  }

  // Sorted by name at every depth, so the order the values were written in does not count;
  // http_build_query writes a link's values and a request's values - strings by then - alike.

  function padCryptQuery ( $vars ) {

    $sort = function ( $list ) use ( &$sort ) {
      ksort ( $list, SORT_STRING );
      foreach ( $list as $key => $value )
        if ( is_array ( $value ) )
          $list [$key] = $sort ( $value );
      return $list;
    };

    return http_build_query ( $sort ( $vars ), '', '&', PHP_QUERY_RFC3986 );

  }

  // The key for one purpose, 'encrypt' or 'sign', derived from the application key.

  function padCryptKey ( $purpose ) {

    $master = padCryptMaster ();

    return ( $master === NULL ) ? NULL : hash_hmac ( 'sha256', "pad $purpose", $master, TRUE );

  }

  // The application key is kept in a static of this function, never in a global: the
  // error reports and dumps show globals. A $padAppKey that is no key is named.

  function padCryptMaster () {

    global $padApp;

    static $keys = [];

    $given = $GLOBALS ['padAppKey'] ?? '';

    if ( $given !== '' and $given !== NULL ) {

      $index = 'given:' . ( is_string ( $given ) ? hash ( 'sha256', $given ) : '' );

      if ( isset ( $keys [$index] ) )
        return $keys [$index];

      $key = is_string ( $given ) ? padCryptKeyText ( $given ) : NULL;

      if ( $key === NULL ) {
        padError ( "\$padAppKey must be 32 bytes, or 'base64:' followed by 32 bytes in base64 - "
                   . "the key file DATA/keys/" . padMakeSafe ( $padApp, 100 ) . ".key holds one to copy" );
        return NULL;
      }

      return $keys [$index] = $key;

    }

    $index = "file:$padApp";

    if ( isset ( $keys [$index] ) )
      return $keys [$index];

    $key = padCryptKeyFile ( DATA . "keys/$padApp.key" );

    return ( $key === NULL ) ? NULL : ( $keys [$index] = $key );

  }

  // A key as $padAppKey and the key file write it; NULL for anything else, the caller
  // says what was wrong.

  function padCryptKeyText ( $text ) {

    if ( str_starts_with ( $text, 'base64:' ) ) {
      $text = base64_decode ( substr ( $text, 7 ), TRUE );
      if ( $text === FALSE )
        return NULL;
    }

    return ( strlen ( $text ) === 32 ) ? $text : NULL;

  }

  // The file holds the key the way $padAppKey takes it, 'base64:...', so it can be copied
  // into the configuration as it is. It is made beside its place and linked in, which
  // fails when another request linked one first: both then read the same key. Mode 0600
  // before a byte of the key is written; its directory 0700.

  function padCryptKeyFile ( $file ) {

    clearstatcache ( TRUE, $file );

    if ( ! is_file ( $file ) ) {

      $dir = dirname ( $file );

      if ( ! is_dir ( $dir ) )
        @mkdir ( $dir, 0700, TRUE );

      $temp   = $dir . '/.' . basename ( $file ) . '.' . bin2hex ( random_bytes ( 6 ) ) . '.tmp';
      $handle = @fopen ( $temp, 'x' );

      if ( $handle ) {

        @chmod ( $temp, 0600 );
        fwrite ( $handle, 'base64:' . base64_encode ( random_bytes ( 32 ) ) . "\n" );
        fclose ( $handle );

        if ( ! @link ( $temp, $file ) and ! is_file ( $file ) )
          @rename ( $temp, $file );

        @unlink ( $temp );

      }

      clearstatcache ( TRUE, $file );

    }

    $text = is_file ( $file ) ? @file_get_contents ( $file ) : FALSE;
    $key  = is_string ( $text ) ? padCryptKeyText ( trim ( $text ) ) : NULL;

    if ( $key === NULL )
      padError ( 'the key file ' . padMakeSafe ( $file, 200 ) . ( is_string ( $text ) ? ' holds no key' : ' cannot be made or read' ) );

    return $key;

  }

  // URL-safe base64 without padding: a sealed value goes into a link, a cookie or a field
  // as it is.

  function padCryptB64 ( $bytes ) {

    return rtrim ( strtr ( base64_encode ( $bytes ), '+/', '-_' ), '=' );

  }

  // Only the URL-safe alphabet - base64_decode skips white space even in its strict mode -
  // and only the one way padCryptB64 writes those bytes: the last character carries bits
  // that decode to nothing, and a payload with its last character changed opened as the
  // untouched one.

  function padCryptUnB64 ( $text ) {

    if ( ! preg_match ( '/^[A-Za-z0-9_-]+$/', $text ) )
      return NULL;

    $bytes = base64_decode ( strtr ( $text, '-_', '+/' ), TRUE );

    return ( $bytes === FALSE or padCryptB64 ( $bytes ) !== $text ) ? NULL : $bytes;

  }

?>
