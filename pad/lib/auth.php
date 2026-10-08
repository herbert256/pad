<?php

  // Logging in: who the visitor is, kept in the session, for every page of the application -
  // what Laravel's Auth, Django's auth and Rails' has_secure_password with a session give,
  // as plain functions. The application keeps its users where it likes - a table, a JSON
  // file, a directory service - and hands the row of the one who logged in to padLogin; the
  // engine keeps it, answers it back, and forgets it again.
  //
  // padLogin               keeps a user row - an array with at least an id - as the one
  //                        logged in to this application; a new session id (fixation), and
  //                        with $remember a remember-me cookie
  // padLogout              forgets the login and the application's session names, gives the
  //                        session a new id, drops the remember-me cookie
  // padUser                the row of the user logged in, or one field of it by name or dot
  //                        path - NULL for a guest
  // padUserId              the id of that user, NULL for a guest
  // padAuthCheck           whether someone is logged in
  // padAttempt             a password checked against its hash and, when it matches, the
  //                        user logged in - with the new hash when the old one is outdated
  // padAuthRequire         lets a logged-in visitor through; anyone else goes to the login
  //                        page, the address they asked for kept
  // padRedirectIntended    after the login: to that address, or to the default page
  // padPasswordToken       a password-reset token for an e-mail address, bound to the
  //                        current password hash and an expiry
  // padPasswordTokenCheck  whether such a token is valid for that address and hash
  //
  // padAuthApp / padAuthStore / padAuthRows / padAuthSecret / padAuthKeep / padAuthSecrets /
  // padAuthSign / padAuthCookieName / padAuthCookie / padAuthSecure / padAuthRemembered /
  // padAuthPage are the private helpers below them.
  //
  // The session cookie is the host's, shared by every application on it, so the login is
  // kept per application: $_SESSION ['padAuth'] [<application>]. Logging in to one is not
  // logging in to another. The fields of the row whose names say password, secret or token
  // never go into the session - {debug} and an error report would show them - and are
  // only used to sign the remember-me cookie.
  //
  // Remember-me without a table of tokens: the cookie holds the user's id and an expiry,
  // signed with an HMAC (the application key, lib/crypt.php) over those two, the
  // application, and the row's secret fields - its password hash. A request without a
  // login that brings the cookie asks the application for the row again, through the
  // function $padAuthRemember names (id in, row or NULL out), checks the signature against
  // that row and logs it in. A changed password - or a remember_token column the
  // application renews - ends every remember-me cookie made before it; a deleted user's
  // finds no row. What a storage-free cookie cannot do is end one stolen copy alone before
  // it expires: padLogout drops the cookie of this browser only.

  const padAuthRememberDays = 30;

  // A bcrypt hash of a random password nobody knows: padAttempt checks a password against
  // it when there is no user, so an unknown name costs about the time a known one does and
  // the answer's timing does not tell which names exist. Cost 10, PHP's default before 8.4.

  const padAuthDummyHash = '$2y$10$wdmCvg/UL8JQI.fIFBlaE.21id6Fbc4b68z.v2d54tagiHzJyzDNO';

  function padLogin ( $user, $remember = FALSE ) {

    $user = padAuthRows ( $user, 'padLogin' );

    if ( $user === NULL )
      return FALSE;

    if ( ! padSessionStart () )
      return padError ( 'padLogin: no session can start - the headers have gone out already' );

    padSessionRegenerate ();

    $_SESSION ['padAuth'] [ padAuthApp () ] ['user'] = padAuthKeep ( $user );

    padAuthStore ( 'now', NULL );
    padAuthStore ( 'checked', TRUE );

    if ( $remember !== FALSE and $remember !== NULL and $remember !== 0 )
      padAuthCookie ( $user, $remember );

    return TRUE;

  }

  // The application's own session names ($padSessionVars) go too, set to NULL so the end of
  // the request takes them out (padCloseSession) - the cart of the one who logged out is no
  // cart of the next one at the same browser. Other applications' logins stay.

  function padLogout () {

    global $padSessionVars;

    $app = padAuthApp ();

    padAuthStore ( 'now', NULL );
    padAuthStore ( 'checked', TRUE );

    padAuthCookie ( NULL, FALSE );

    if ( ! padSessionExists () or ! padSessionStart () )
      return TRUE;

    unset ( $_SESSION ['padAuth'] [$app] );

    foreach ( (array) ( $padSessionVars ?? [] ) as $name )
      if ( is_string ( $name ) and ! str_starts_with ( $name, 'pad' ) )
        $GLOBALS [$name] = NULL;

    padSessionRegenerate ();

    return TRUE;

  }

  // Reading never starts a session the visitor did not bring - a guest's page makes no
  // session - but a request without a login that brings the remember-me cookie logs in here,
  // the first time anything asks.

  function padUser ( $field = NULL ) {

    $user = padAuthStore ( 'now' );

    if ( $user === NULL and padSessionExists () and padSessionStart () )
      $user = $_SESSION ['padAuth'] [ padAuthApp () ] ['user'] ?? NULL;

    if ( ! is_array ( $user ) )
      $user = padAuthRemembered ();

    if ( ! is_array ( $user ) )
      return NULL;

    if ( $field === NULL )
      return $user;

    $field = padRequestKey ( $field, 'padUser' );

    if ( $field === NULL )
      return NULL;

    [ $found, $value ] = padRequestFind ( $user, $field );

    return $found ? $value : NULL;

  }

  function padUserId () {

    return padUser ( 'id' );

  }

  function padAuthCheck () {

    return padUser () !== NULL;

  }

  // The login form's work in one call: the password the visitor typed, the hash the
  // application keeps for the name they typed, the row to log in. FALSE for a wrong
  // password and for no user at all ($hash NULL) - checked against a hash of nobody's, so
  // both take the same time. When the hash was made with older settings than PHP's
  // default now, $rehash receives a new one for the application to store; NULL otherwise.
  //
  // Rate limiting stays the application's, in front of it:
  //
  //   if ( ! padRateLimit ( 'login:' . $_SERVER ['REMOTE_ADDR'], 5, 60 ) ) padAbort ( 429 );
  //   if ( padAttempt ( $password, $row ['password'] ?? NULL, $row, $remember, $new ) ) {
  //     if ( $new ) db ( "UPDATE users SET password={1} WHERE id={2}", [ $new, $row ['id'] ] );
  //     padRedirectIntended ();
  //   }

  function padAttempt ( $password, $hash, $user, $remember = FALSE, &$rehash = NULL ) {

    $rehash = NULL;

    if ( ! is_string ( $hash ) or $hash === '' or ! is_array ( $user ) ) {
      padHashCheck ( (string) ( is_scalar ( $password ) ? $password : '' ), padAuthDummyHash );
      return FALSE;
    }

    if ( ! padHashCheck ( $password, $hash ) )
      return FALSE;

    if ( padHashNeedsRehash ( $hash ) )
      $rehash = padHash ( $password );

    return padLogin ( $user, $remember );

  }

  // A page for the logged-in only, at the top of its PHP or in a _guard.php. The address a
  // guest asked for is kept - for a GET only: a post cannot be asked again - and the guest
  // goes to the login page, from where padRedirectIntended brings them back.

  function padAuthRequire ( $login = 'login' ) {

    if ( padAuthCheck () )
      return TRUE;

    if ( ! is_string ( $login ) or ! preg_match ( '#^\??[A-Za-z0-9_][A-Za-z0-9_./-]*$#D', $login ) or str_contains ( $login, '..' ) ) {
      padError ( 'padAuthRequire: the login page must be a page name, not ' . padRequestShow ( $login ) );
      $login = 'login';
    }

    if ( padRequestMethod () === 'GET' and padSessionStart () )
      $_SESSION ['padAuth'] [ padAuthApp () ] ['intended'] = padAuthPage ();

    padRedirect ( ltrim ( $login, '?' ) );

  }

  // Back to the address padAuthRequire kept - a page of this application, rebuilt from its
  // name and values, so it is never another site - or to the default page. Used once.

  function padRedirectIntended ( $default = 'index' ) {

    $app      = padAuthApp ();
    $intended = NULL;

    if ( padSessionExists () and padSessionStart () ) {
      $intended = $_SESSION ['padAuth'] [$app] ['intended'] ?? NULL;
      unset ( $_SESSION ['padAuth'] [$app] ['intended'] );
    }

    if ( is_array ( $intended ) and is_string ( $intended ['page'] ?? NULL ) and is_array ( $intended ['query'] ?? NULL ) ) {

      $go = implode ( '/', array_map ( 'rawurlencode', explode ( '/', $intended ['page'] ) ) );

      if ( count ( $intended ['query'] ) )
        $go .= '&' . http_build_query ( $intended ['query'], '', '&', PHP_QUERY_RFC3986 );

      padRedirect ( $go );

    }

    if ( ! is_string ( $default ) or ! preg_match ( '#^\??[A-Za-z0-9_][A-Za-z0-9_./-]*$#D', $default ) or str_contains ( $default, '..' ) ) {
      padError ( 'padRedirectIntended: the default page must be a page name, not ' . padRequestShow ( $default ) );
      $default = 'index';
    }

    padRedirect ( ltrim ( $default, '?' ) );

  }

  // A password-reset link without a table of tokens: the token is an expiry and an HMAC
  // over the application, the address (trimmed, lower case) and that expiry - and over the
  // password hash the user has now. Setting the new password changes the hash, a new salt
  // each time, so a token works once: after the reset it no longer matches. It also ends
  // when the time is up, and when the user changes the password some other way. The
  // application mails a link like {$padGoExt}reset&email=...&token=... with it.
  //
  //   $token = padPasswordToken ( $email, $row ['password'] );           // the mail
  //   padPasswordTokenCheck ( $token, $email, $row ['password'] )        // the reset page
  //
  // $ttl is in seconds, an hour by default.

  function padPasswordToken ( $email, $passwordHash, $ttl = 3600 ) {

    if ( ! is_string ( $email ) or trim ( $email ) === '' ) {
      padError ( 'padPasswordToken: the e-mail address must be a text, not ' . padRequestShow ( $email ) );
      return '';
    }

    if ( ! is_string ( $passwordHash ) ) {
      padError ( 'padPasswordToken: the password hash must be a text, not ' . get_debug_type ( $passwordHash ) );
      return '';
    }

    if ( ! is_int ( $ttl ) or $ttl < 1 ) {
      padError ( 'padPasswordToken: the time the token lives must be a number of seconds, not ' . padRequestShow ( $ttl ) );
      return '';
    }

    $until = padNow ()->getTimestamp () + $ttl;
    $sign  = padAuthSign ( 'password', [ strtolower ( trim ( $email ) ), $until ], $passwordHash );

    return ( $sign === NULL ) ? '' : base_convert ( (string) $until, 10, 36 ) . '.' . padCryptB64 ( $sign );

  }

  // The token comes from a link - the visitor's to change - so a wrong one is FALSE, never
  // an error.

  function padPasswordTokenCheck ( $token, $email, $passwordHash ) {

    if ( ! is_string ( $token ) or ! is_string ( $email ) or ! is_string ( $passwordHash ) )
      return FALSE;

    if ( ! preg_match ( '/^([0-9a-z]{1,12})\.([A-Za-z0-9_-]{43})$/D', trim ( $token ), $match ) )
      return FALSE;

    $until = (int) base_convert ( $match [1], 36, 10 );

    if ( $until < padNow ()->getTimestamp () )
      return FALSE;

    $sign = padAuthSign ( 'password', [ strtolower ( trim ( $email ) ), $until ], $passwordHash );

    return $sign !== NULL and hash_equals ( padCryptB64 ( $sign ), $match [2] );

  }

  // ------------------------------------------------------------------------------------
  // The private helpers.
  // ------------------------------------------------------------------------------------

  function padAuthApp () {

    return (string) ( $GLOBALS ['padApp'] ?? '' );

  }

  // This request's own memory, in a static rather than a global - error reports and dumps
  // show globals: 'now' a user that is logged in for this request without a session behind
  // it (a remember-me login after the headers went out), 'checked' that the remember-me
  // cookie was looked at.

  function padAuthStore ( $key, $value = '' ) {

    static $store = [];

    if ( func_num_args () > 1 )
      $store [$key] = $value;

    return $store [$key] ?? NULL;

  }

  // A user row: an array, or an object read as one, with an id that is a number or a text.

  function padAuthRows ( $user, $function ) {

    if ( is_object ( $user ) )
      $user = padToArray ( $user );

    if ( ! is_array ( $user ) ) {
      padError ( "$function: the user must be an array with an id, not " . get_debug_type ( $user ) );
      return NULL;
    }

    $id = $user ['id'] ?? NULL;

    if ( ! is_int ( $id ) and ! ( is_string ( $id ) and $id !== '' ) ) {
      padError ( "$function: the user has no id - a number or a text in the field 'id'" );
      return NULL;
    }

    return $user;

  }

  // The names of the fields kept out of the session and signed into the cookie instead.

  function padAuthSecret ( $name ) {

    return (bool) preg_match ( '/pass(word|wd|phrase)|^pass$|pwd|secret|token/i', (string) $name );

  }

  function padAuthKeep ( $user ) {

    foreach ( $user as $name => $value )
      if ( padAuthSecret ( $name ) )
        unset ( $user [$name] );

    return $user;

  }

  function padAuthSecrets ( $user ) {

    $secrets = [];

    foreach ( $user as $name => $value )
      if ( padAuthSecret ( $name ) )
        $secrets [$name] = $value;

    ksort ( $secrets, SORT_STRING );

    return json_encode ( $secrets, JSON_PARTIAL_OUTPUT_ON_ERROR );

  }

  // An HMAC with a key of its own purpose (lib/crypt.php), over the application, the values
  // and a secret - raw bytes, NULL when there is no application key.

  function padAuthSign ( $purpose, $values, $secret ) {

    $key = padCryptKey ( "auth $purpose" );

    if ( $key === NULL )
      return NULL;

    return hash_hmac ( 'sha256', padAuthApp () . "\n" . json_encode ( $values ) . "\n" . $secret, $key, TRUE );

  }

  // One cookie per application, as the login is: the name carries the application.

  function padAuthCookieName () {

    return 'padRemember_' . preg_replace ( '/[^A-Za-z0-9]/', '_', padAuthApp () );

  }

  // Sets the remember-me cookie for a user ($remember TRUE: 30 days, a number: seconds),
  // or drops it ($user NULL).

  function padAuthCookie ( $user, $remember ) {

    $name = padAuthCookieName ();

    if ( $user === NULL ) {

      unset ( $_COOKIE [$name] );

      if ( ! headers_sent () )
        setcookie ( $name, '', [ 'expires' => 1, 'path' => '/', 'httponly' => TRUE, 'samesite' => 'Lax',
                                 'secure' => padAuthSecure () ] );

      return;

    }

    if ( ! function_exists ( (string) ( $GLOBALS ['padAuthRemember'] ?? '' ) ) ) {
      padError ( 'padLogin: remember-me needs $padAuthRemember, the name of a function that finds a user by id' );
      return;
    }

    $seconds = ( $remember === TRUE ) ? padAuthRememberDays * 86400 : $remember;

    if ( ! is_int ( $seconds ) or $seconds < 1 ) {
      padError ( 'padLogin: remember must be TRUE or a number of seconds, not ' . padRequestShow ( $remember ) );
      return;
    }

    $until = padNow ()->getTimestamp () + $seconds;
    $sign  = padAuthSign ( 'remember', [ $user ['id'], $until ], padAuthSecrets ( $user ) );

    if ( $sign === NULL or headers_sent () )
      return;

    $value = padCryptB64 ( json_encode ( [ $user ['id'], $until ] ) ) . '.' . padCryptB64 ( $sign );

    $_COOKIE [$name] = $value;

    setcookie ( $name, $value, [ 'expires' => $until, 'path' => '/', 'httponly' => TRUE, 'samesite' => 'Lax',
                                 'secure' => padAuthSecure () ] );

  }

  function padAuthSecure () {

    return ( $_SERVER ['HTTPS'] ?? '' ) !== '' and ( $_SERVER ['HTTPS'] ?? '' ) !== 'off';

  }

  // The remember-me cookie, read once per request: a signature that holds for the row the
  // application finds for the id logs that row in. A cookie that does not hold is dropped.

  function padAuthRemembered () {

    if ( padAuthStore ( 'checked' ) )
      return padAuthStore ( 'now' );

    padAuthStore ( 'checked', TRUE );

    $cookie = $_COOKIE [ padAuthCookieName () ] ?? NULL;
    $finder = (string) ( $GLOBALS ['padAuthRemember'] ?? '' );

    if ( ! is_string ( $cookie ) or $finder === '' or ! function_exists ( $finder ) )
      return NULL;

    $parts = explode ( '.', $cookie );
    $data  = ( count ( $parts ) == 2 ) ? json_decode ( (string) padCryptUnB64 ( $parts [0] ), TRUE ) : NULL;
    $user  = NULL;

    if ( is_array ( $data ) and count ( $data ) == 2 and is_int ( $data [1] ) and $data [1] >= padNow ()->getTimestamp ()
         and ( is_int ( $data [0] ) or is_string ( $data [0] ) ) )
      $user = $finder ( $data [0] );

    if ( is_object ( $user ) )
      $user = padToArray ( $user );

    $sign = ( is_array ( $user ) and is_scalar ( $user ['id'] ?? NULL ) and (string) $user ['id'] === (string) $data [0] )
          ? padAuthSign ( 'remember', [ $data [0], $data [1] ], padAuthSecrets ( $user ) ) : NULL;

    if ( $sign === NULL or ! hash_equals ( padCryptB64 ( $sign ), $parts [1] ) ) {
      padAuthCookie ( NULL, FALSE );
      return NULL;
    }

    if ( ! headers_sent () and padSessionStart () ) {
      padSessionRegenerate ();
      $_SESSION ['padAuth'] [ padAuthApp () ] ['user'] = padAuthKeep ( $user );
    } else
      padAuthStore ( 'now', padAuthKeep ( $user ) );

    return padAuthKeep ( $user );

  }

  // The page this request was asked by, with its query values - those of the engine (pad*)
  // and the bare key naming the page left out - for padRedirectIntended to rebuild.

  function padAuthPage () {

    global $padPage, $padPageAsked, $padPageKey;

    $query = [];

    foreach ( ( is_array ( $_GET ?? NULL ) ? $_GET : [] ) as $key => $value )
      if ( ! str_starts_with ( (string) $key, 'pad' ) and ! ( (string) $key === (string) ( $padPageKey ?? '' ) and $value === '' ) )
        $query [$key] = $value;

    return [ 'page' => ( $padPageAsked ?? '' ) !== '' ? $padPageAsked : (string) $padPage, 'query' => $query ];

  }

?>
