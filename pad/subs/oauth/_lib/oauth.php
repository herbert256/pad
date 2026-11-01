<?php

  // The OAuth subsystem: the authorization code flow of RFC 6749 with PKCE (RFC 7636, S256),
  // and OpenID Connect where the provider speaks it - for padOAuthStart, padOAuthUrl and
  // padOAuthUser (lib/oauth.php). A provider is a file of subs/oauth/types/, included with
  // the caller's $options at hand, answering an array:
  //
  //   name       what the button says: Log in with <name>
  //   icon       the icon of subs/icons/ the button shows when there is one of that name
  //   authorize  the authorize endpoint, token the token endpoint, userinfo the user's
  //              endpoint ('' - the id_token's claims are the user, as Apple's)
  //   discovery  TRUE: the three come from <issuer>/.well-known/openid-configuration
  //   instance   the address of the provider's instance when it is not its default one - a
  //              GitLab of one's own - '' for the default: it goes into the user's id, as a
  //              sub is unique within its instance alone; a discovery provider's is its issuer
  //   issuer     the id_token's iss; {tid} in it stands for the token's own tid claim
  //   claims     claims the id_token must carry with these values
  //   oidc       an OpenID Connect provider: a nonce, and the id_token checked
  //   scopes     the scopes asked for unless the options name others
  //   pkce       whether the PKCE challenge goes along
  //   post       the provider posts its answer back (response_mode form_post)
  //   params     more values for the authorize address
  //   needs      the settings - option name => the padEnv name read when no option gives it
  //   secret     a function making the client secret from the settings (Apple's JWT)
  //   headers    more headers for the provider's API
  //   user       the normalised user's fields - id, name, email, avatar, username - as a
  //              path in the provider's answer each, alternatives split by |; verified the
  //              path of the provider's word that the address is the account's own (TRUE or
  //              'true' - the first of the paths the answer holds decides)
  //   more       a function that completes the user - function ( $user, $answer, $get,
  //              $callback ): GitHub's private address, Apple's first-login name; an
  //              email_verified it answers counts as the verified path's
  //
  // The state (24 random bytes), the PKCE verifier and the nonce are kept in the session
  // under padOAuth/<application>/<state>, at most five logins at once - two tabs - and ten
  // minutes long; the answer that brings the state takes its record out, so a state works
  // once. For a provider that posts its answer back - Apple - the record goes into a sealed
  // cookie as well (padEncrypt, SameSite=None, Secure, HttpOnly), as a post from another
  // site brings no SameSite=Lax session cookie. The record keeps the token endpoint and the
  // issuer the login went to, and the way back must resolve to the same: handled with other
  // options - another base=, issuer= or tenant= - it would send the code, the verifier and
  // the client secret to another instance and log its user in (RFC 9207's mix-up).
  //
  // The id_token of OpenID Connect is checked for its issuer, its audience (and azp when it
  // names several), its expiry and the nonce kept. Its signature is not checked: the token
  // comes straight from the token endpoint over TLS, in this server's own request, and
  // OpenID Connect Core 1.0, section 3.1.3.7, item 6, lets TLS server validation stand for
  // the signature then. A provider whose tokens reach the application by another road -
  // the implicit flow, a token handed in by a browser - is not what this is for.
  //
  // Every fetch goes through padCurl, so a test answers them with padCurlFake (lib/curlFake.php)
  // and nothing reaches the provider; the discovery document is kept a day (padCacheGet),
  // except while the fetches are faked.

  const padOAuthKeep = 600;

  // The provider of a kind, its settings read, or NULL - an unknown kind or a missing setting
  // is the author's: a padError under the strict check, and padOAuthError says it either way.

  function padOAuthProvider ( $kind, $options, $function ) {

    padOAuthStore ( 'error', '' );

    if ( ! is_array ( $options ) )
      return padOAuthAuthor ( "$function: the options are an array - [ 'scopes' => 'read:user' ] - not " . get_debug_type ( $options ) );

    if ( ! is_string ( $kind ) or ! padKind ( 'oauth', $kind ) )
      return padOAuthAuthor ( "$function: there is no OAuth provider named '" . padMakeSafe ( is_scalar ( $kind ) ? (string) $kind : get_debug_type ( $kind ), 30 )
                              . "' - " . implode ( ', ', padKinds ( 'oauth' ) ) );

    $provider = padOAuthInclude ( PAD . "subs/oauth/types/$kind.php", $options );

    $provider += [ 'name' => $kind, 'icon' => '', 'authorize' => '', 'token' => '', 'userinfo' => '', 'discovery' => FALSE, 'instance' => '',
                   'issuer' => '', 'claims' => [], 'oidc' => FALSE, 'scopes' => '', 'pkce' => TRUE, 'post' => FALSE,
                   'params' => [], 'needs' => [], 'secret' => NULL, 'headers' => [], 'user' => [], 'more' => NULL ];

    $provider ['kind'] = $kind;

    // Each setting from the options, else from the environment - and every one missing named
    // at once, with the variable to set.

    $settings = [];
    $missing  = [];

    foreach ( $provider ['needs'] as $option => $env ) {
      $value = $options [$option] ?? padEnv ( $env, '' );
      if ( ! is_scalar ( $value ) or trim ( (string) $value ) === '' )
        $missing [$option] = $env;
      else
        $settings [$option] = trim ( (string) $value );
    }

    if ( $missing )
      return padOAuthAuthor ( "$function: Log in with " . $provider ['name'] . ' needs ' . implode ( ' and ', $missing )
                              . " - set it in the application's .env, or give the option" . ( count ( $missing ) > 1 ? 's ' : ' ' )
                              . implode ( ', ', array_keys ( $missing ) ) );

    $provider ['settings'] = $settings;
    $provider ['auth']     = 'post';

    if ( $provider ['discovery'] ) {

      $doc = padOAuthDiscover ( $settings ['issuer'] ?? '' );

      if ( ! is_array ( $doc ) )
        return NULL;

      $provider ['issuer']    = $doc ['issuer'];
      $provider ['instance']  = $doc ['issuer'];
      $provider ['authorize'] = (string) ( $doc ['authorization_endpoint'] ?? '' );
      $provider ['token']     = (string) ( $doc ['token_endpoint'] ?? '' );
      $provider ['userinfo']  = (string) ( $doc ['userinfo_endpoint'] ?? '' );

      // client_secret_post unless the provider takes only client_secret_basic - the default
      // of a document that names neither (OpenID Connect Discovery 1.0, section 3).

      $methods = (array) ( $doc ['token_endpoint_auth_methods_supported'] ?? [ 'client_secret_basic' ] );

      if ( ! in_array ( 'client_secret_post', $methods, TRUE ) and in_array ( 'client_secret_basic', $methods, TRUE ) )
        $provider ['auth'] = 'basic';

      if ( $provider ['authorize'] === '' or $provider ['token'] === '' )
        return padOAuthFail ( 'the OpenID configuration of ' . padMakeSafe ( $doc ['issuer'], 80 ) . ' names no authorization or token endpoint' );

    }

    return $provider;

  }

  function padOAuthInclude ( $padOAuthFile, $options ) {

    $answer = include $padOAuthFile;

    return is_array ( $answer ) ? $answer : [];

  }

  // The address of the provider's authorize page, the login's record kept - or '' when it
  // cannot be made.

  function padOAuthAuthorize ( $kind, $options, $function ) {

    $provider = padOAuthProvider ( $kind, $options, $function );

    if ( ! is_array ( $provider ) )
      return '';

    $redirect = padOAuthRedirect ( $options, $function );

    if ( $redirect === '' )
      return '';

    $scopes = $options ['scopes'] ?? $provider ['scopes'];
    $scopes = is_array ( $scopes ) ? implode ( ' ', $scopes ) : trim ( (string) $scopes );

    $record = [ 'kind'     => $kind,
                'app'      => (string) ( $GLOBALS ['padApp'] ?? '' ),
                'redirect' => $redirect,
                'verifier' => $provider ['pkce'] ? padCryptB64 ( random_bytes ( 32 ) ) : '',
                'nonce'    => $provider ['oidc'] ? padCryptB64 ( random_bytes ( 16 ) ) : '',
                'token'    => $provider ['token'],
                'issuer'   => $provider ['issuer'],
                'time'     => time () ];

    $state  = padCryptB64 ( random_bytes ( 24 ) );
    $params = [ 'response_type' => 'code',
                'client_id'     => $provider ['settings'] ['client_id'],
                'redirect_uri'  => $redirect ];

    if ( $scopes !== '' )
      $params ['scope'] = $scopes;

    $params ['state'] = $state;

    if ( $record ['verifier'] !== '' ) {
      $params ['code_challenge']        = padCryptB64 ( hash ( 'sha256', $record ['verifier'], TRUE ) );
      $params ['code_challenge_method'] = 'S256';
    }

    if ( $record ['nonce'] !== '' )
      $params ['nonce'] = $record ['nonce'];

    $params = array_merge ( $params, (array) $provider ['params'], (array) ( $options ['params'] ?? [] ) );

    if ( ! padOAuthKeep ( $state, $record, $provider ['post'] ) )
      return '';

    return $provider ['authorize'] . ( str_contains ( $provider ['authorize'], '?' ) ? '&' : '?' )
         . http_build_query ( $params, '', '&', PHP_QUERY_RFC3986 );

  }

  // Where the provider sends the visitor back: the option redirect, an absolute http(s)
  // address registered with the provider - else this page, by the name it was asked by, on
  // this application's own address ($padGoExt): ?oauth/github, or oauth/github with
  // $padCleanUrls. It must be the same address on the way out and on the way back, so the
  // record keeps it.

  function padOAuthRedirect ( $options, $function ) {

    global $padGoExt, $padPage, $padPageAsked;

    if ( isset ( $options ['redirect'] ) ) {

      $redirect = $options ['redirect'];

      if ( ! is_string ( $redirect ) or ! preg_match ( '~^https?://[^\s/?#]+[^\s]*$~iD', $redirect ) ) {
        padOAuthAuthor ( "$function: the option redirect is the absolute address the provider sends the visitor back to - not "
                         . padMakeSafe ( is_scalar ( $redirect ) ? (string) $redirect : get_debug_type ( $redirect ), 60 ) );
        return '';
      }

      return $redirect;

    }

    $page = ( ( $padPageAsked ?? '' ) !== '' ) ? $padPageAsked : (string) ( $padPage ?? '' );

    return (string) $padGoExt . implode ( '/', array_map ( 'rawurlencode', explode ( '/', $page ) ) );

  }

  function padOAuthKeep ( $state, $record, $post ) {

    if ( ! padSessionStart () ) {
      padOAuthFail ( 'no session can start - the headers have gone out already' );
      return FALSE;
    }

    $app  = $record ['app'];
    $list = $_SESSION ['padOAuth'] [$app] ?? [];
    $list = is_array ( $list ) ? $list : [];

    $list [$state] = $record;

    $_SESSION ['padOAuth'] [$app] = array_slice ( $list, -5, NULL, TRUE );

    if ( $post and ! headers_sent () )
      setcookie ( 'padOAuth', padEncrypt ( [ 'state' => $state ] + $record ),
                  [ 'expires' => time () + padOAuthKeep, 'path' => '/', 'secure' => TRUE, 'httponly' => TRUE, 'samesite' => 'None' ] );

    return TRUE;

  }

  // The record of a state, taken out - in the session, else in the sealed cookie - or NULL.

  function padOAuthTake ( $state, $kind ) {

    $app    = (string) ( $GLOBALS ['padApp'] ?? '' );
    $record = NULL;

    if ( $state === '' )
      return NULL;

    if ( padSessionExists () and padSessionStart () and isset ( $_SESSION ['padOAuth'] [$app] [$state] ) ) {
      $record = $_SESSION ['padOAuth'] [$app] [$state];
      unset ( $_SESSION ['padOAuth'] [$app] [$state] );
    }

    if ( $record === NULL and isset ( $_COOKIE ['padOAuth'] ) ) {
      $sealed = padDecrypt ( $_COOKIE ['padOAuth'] );
      if ( is_array ( $sealed ) and hash_equals ( (string) ( $sealed ['state'] ?? '' ), $state ) )
        $record = $sealed;
    }

    if ( isset ( $_COOKIE ['padOAuth'] ) and ! headers_sent () )
      setcookie ( 'padOAuth', '', [ 'expires' => 1, 'path' => '/', 'secure' => TRUE, 'httponly' => TRUE, 'samesite' => 'None' ] );

    if ( ! is_array ( $record ) or ( $record ['kind'] ?? '' ) !== $kind or ( $record ['app'] ?? '' ) !== $app
         or (int) ( $record ['time'] ?? 0 ) < time () - padOAuthKeep )
      return NULL;

    return $record;

  }

  // The way back from the provider: the user, or FALSE.

  function padOAuthCallback ( $kind, $options ) {

    $provider = padOAuthProvider ( $kind, $options, 'padOAuthUser' );

    if ( ! is_array ( $provider ) )
      return FALSE;

    padOAuthStore ( 'token', NULL );

    $name     = $provider ['name'];
    $callback = ( isset ( $_POST ['state'] ) or isset ( $_POST ['code'] ) or isset ( $_POST ['error'] ) ) ? $_POST : $_GET;
    $state    = is_string ( $callback ['state'] ?? NULL ) ? $callback ['state'] : '';
    $record   = padOAuthTake ( $state, $kind );

    if ( isset ( $callback ['error'] ) ) {
      $error = padOAuthText ( $callback ['error'] );
      $about = padOAuthText ( $callback ['error_description'] ?? '' );
      return padOAuthFail ( ( $error === 'access_denied' ? "the login at $name was refused" : "$name answered with an error" )
                            . " ($error" . ( $about !== '' ? ": $about" : '' ) . ')' );
    }

    if ( $record === NULL )
      return padOAuthFail ( "the answer from $name does not match a login started here - its state is unknown, used or over ten minutes old; start the login again" );

    if ( ( $record ['token'] ?? NULL ) !== $provider ['token'] or ( $record ['issuer'] ?? NULL ) !== $provider ['issuer'] )
      return padOAuthFail ( "the answer from $name belongs to a login started at another address of it - the way back is handled with other options than the start; start the login again" );

    $code = is_string ( $callback ['code'] ?? NULL ) ? $callback ['code'] : '';

    if ( $code === '' )
      return padOAuthFail ( "the answer from $name holds no code" );

    // The code for the tokens - the client authenticating in the body (client_secret_post),
    // or with HTTP Basic where the provider takes only that.

    $settings = $provider ['settings'];
    $secret   = is_callable ( $provider ['secret'] ) ? (string) ( $provider ['secret'] ) ( $settings ) : ( $settings ['client_secret'] ?? '' );

    if ( $secret === '' )
      return padOAuthFail ( "no client secret could be made for $name - check its key" );

    $body = [ 'grant_type' => 'authorization_code', 'code' => $code, 'redirect_uri' => $record ['redirect'] ];

    if ( $provider ['auth'] == 'post' )
      $body += [ 'client_id' => $settings ['client_id'], 'client_secret' => $secret ];
    else
      $body += [ 'client_id' => $settings ['client_id'] ];

    if ( ( $record ['verifier'] ?? '' ) !== '' )
      $body ['code_verifier'] = $record ['verifier'];

    $basic  = ( $provider ['auth'] == 'basic' ) ? [ $settings ['client_id'], $secret ] : NULL;
    $answer = padOAuthFetch ( 'POST', $provider ['token'], $body, [ 'Accept' => 'application/json' ], $basic );
    $token  = $answer ['json'];

    if ( ! is_array ( $token ) or isset ( $token ['error'] ) or ! is_string ( $token ['access_token'] ?? NULL ) or $answer ['status'] < 200 or $answer ['status'] > 299 )
      return padOAuthFail ( "$name did not give the tokens for the code (" . padOAuthProblem ( $answer ) . ')' );

    padOAuthStore ( 'token', $token );

    // OpenID Connect: the id_token's claims, checked - see the top of this file on why its
    // signature is not.

    $claims = [];

    if ( $provider ['oidc'] ) {
      $claims = padOAuthIdToken ( $provider, $token ['id_token'] ?? NULL, $record ['nonce'] ?? '' );
      if ( is_string ( $claims ) )
        return padOAuthFail ( "the id_token of $name $claims" );
    }

    $headers = (array) $provider ['headers'] + [ 'Accept' => 'application/json' ];
    $headers ['Authorization'] = 'Bearer ' . $token ['access_token'];

    $get = function ( $url ) use ( $headers ) {
      $one = padOAuthFetch ( 'GET', $url, NULL, $headers );
      return ( $one ['status'] >= 200 and $one ['status'] <= 299 ) ? $one ['json'] : NULL;
    };

    if ( $provider ['userinfo'] !== '' ) {

      $info = padOAuthFetch ( 'GET', $provider ['userinfo'], NULL, $headers );

      if ( ! is_array ( $info ['json'] ) or $info ['status'] < 200 or $info ['status'] > 299 )
        return padOAuthFail ( "$name did not give the user (" . padOAuthProblem ( $info ) . ')' );

      // OpenID Connect Core 5.3.2: the userinfo's sub must be the id_token's.

      if ( $provider ['oidc'] and (string) ( $info ['json'] ['sub'] ?? '' ) !== (string) ( $claims ['sub'] ?? '' ) )
        return padOAuthFail ( "the user $name gave is not the one of its id_token" );

      $answer = $info ['json'] + $claims;

    } else

      $answer = $claims;

    $user = padOAuthMap ( $kind, $provider ['user'], $answer );

    if ( is_callable ( $provider ['more'] ) ) {
      $more = ( $provider ['more'] ) ( $user, $answer, $get, $callback );
      if ( is_array ( $more ) ) {
        $verified = array_key_exists ( 'email_verified', $more ) ? padOAuthYes ( $more ['email_verified'] ) : $user ['email_verified'];
        $user     = array_map ( fn ( $value ) => is_scalar ( $value ) ? trim ( (string) $value ) : '', $more ) + $user;
        $user ['email_verified'] = $verified;
      }
    }

    // The address is a contact; that the provider vouches for it - email_verified - is what
    // makes it the account's, the one thing an application may link an existing account by.

    $user ['email_verified'] = ( $user ['email'] !== '' and $user ['email_verified'] === TRUE );

    if ( $user ['sub'] === '' )
      return padOAuthFail ( "the user $name gave has no id" );

    $user ['provider'] = $kind;
    $user ['id']       = padOAuthId ( $kind, (string) $provider ['instance'], $user ['sub'] );

    return [ 'provider' => $user ['provider'], 'id'       => $user ['id'],       'sub'            => $user ['sub'],
             'name'     => $user ['name'],     'email'    => $user ['email'],    'email_verified' => $user ['email_verified'],
             'avatar'   => $user ['avatar'],   'username' => $user ['username'] ];

  }

  // The user's id: the kind and the sub for the provider's default instance - github:583231 -
  // else the kind, the instance's address and the sub - gitlab@git.example.org:42 - as a sub
  // is unique within its instance alone (OpenID Connect Core 2: within the issuer), and a
  // GitLab of one's own counts its users from 1 as gitlab.com does. The address without its
  // https://, its scheme and host in lower case, a : and a % in it written %3A and %25: the
  // first : after the kind ends the instance, so no two instances and subs read alike.

  function padOAuthId ( $kind, $instance, $sub ) {

    $instance = rtrim ( $instance, '/' );

    if ( $instance === '' )
      return "$kind:$sub";

    $instance = preg_replace_callback ( '~^[a-z][a-z0-9+.-]*://[^/?#]*~i', fn ( $match ) => strtolower ( $match [0] ), $instance );
    $instance = preg_replace ( '~^https://~', '', $instance );

    return $kind . '@' . strtr ( $instance, [ '%' => '%25', ':' => '%3A' ] ) . ':' . $sub;

  }

  // The fields of the normalised user from the provider's answer: each a path - a dot path for
  // a nested value - or several split by |, the first that holds a value.

  function padOAuthMap ( $kind, $map, $answer ) {

    $user = [];

    foreach ( [ 'id', 'name', 'email', 'avatar', 'username' ] as $field ) {

      $value = '';

      foreach ( explode ( '|', (string) ( $map [$field] ?? '' ) ) as $path ) {

        $here = ( $path === '' ) ? NULL : $answer;

        foreach ( ( $path === '' ) ? [] : explode ( '.', $path ) as $step )
          $here = is_array ( $here ) ? ( $here [$step] ?? NULL ) : NULL;

        if ( ( is_string ( $here ) or is_int ( $here ) or is_float ( $here ) ) and trim ( (string) $here ) !== '' ) {
          $value = trim ( (string) $here );
          break;
        }

      }

      $user [ $field == 'id' ? 'sub' : $field ] = $value;

    }

    $verified = NULL;

    foreach ( explode ( '|', (string) ( $map ['verified'] ?? '' ) ) as $path ) {

      $here = ( $path === '' ) ? NULL : $answer;

      foreach ( ( $path === '' ) ? [] : explode ( '.', $path ) as $step )
        $here = is_array ( $here ) ? ( $here [$step] ?? NULL ) : NULL;

      if ( $here !== NULL ) {
        $verified = $here;
        break;
      }

    }

    $user ['email_verified'] = padOAuthYes ( $verified );

    return $user;

  }

  // A provider's yes: TRUE, or the text 'true' Apple sends - nothing else.

  function padOAuthYes ( $value ) {

    return $value === TRUE or ( is_string ( $value ) and strtolower ( trim ( $value ) ) === 'true' );

  }

  // Whether a list of addresses as GitHub's and Forgejo's /user/emails give it - email,
  // verified, primary - has this address verified.

  function padOAuthListed ( $emails, $email ) {

    foreach ( is_array ( $emails ) ? $emails : [] as $one )
      if ( is_array ( $one ) and ( $one ['verified'] ?? NULL ) === TRUE and is_string ( $one ['email'] ?? NULL )
           and $email !== '' and strcasecmp ( $one ['email'], $email ) == 0 )
        return TRUE;

    return FALSE;

  }

  // The checks of OpenID Connect Core 3.1.3.7 an application relies on - the claims, or what
  // is wrong with the token as text.

  function padOAuthIdToken ( $provider, $jwt, $nonce ) {

    include_once PAD . 'subs/oauth/_lib/jwt.php';

    if ( ! is_string ( $jwt ) or $jwt === '' )
      return 'is missing';

    $claims = padJwtClaims ( $jwt );

    if ( $claims === NULL )
      return 'cannot be read';

    $clientId = $provider ['settings'] ['client_id'];
    $issuer   = str_replace ( '{tid}', is_string ( $claims ['tid'] ?? NULL ) ? $claims ['tid'] : "\0", $provider ['issuer'] );
    $audience = $claims ['aud'] ?? NULL;
    $audience = is_array ( $audience ) ? $audience : [ $audience ];

    if ( $provider ['issuer'] !== '' and ( $claims ['iss'] ?? NULL ) !== $issuer )
      return 'comes from another issuer: ' . padMakeSafe ( is_string ( $claims ['iss'] ?? NULL ) ? $claims ['iss'] : '?', 80 );

    if ( ! in_array ( $clientId, $audience, TRUE ) )
      return 'is meant for another client';

    if ( count ( $audience ) > 1 and ( $claims ['azp'] ?? NULL ) !== $clientId )
      return 'is meant for several clients and not authorized for this one';

    if ( ! is_numeric ( $claims ['exp'] ?? NULL ) or (int) $claims ['exp'] < time () - 60 )
      return 'has expired';

    if ( $nonce !== '' and ! hash_equals ( $nonce, is_string ( $claims ['nonce'] ?? NULL ) ? $claims ['nonce'] : '' ) )
      return 'does not carry the nonce of this login';

    foreach ( (array) $provider ['claims'] as $claim => $value )
      if ( strtolower ( (string) ( $claims [$claim] ?? '' ) ) !== strtolower ( (string) $value ) )
        return "has another $claim";

    return $claims;

  }

  // The discovery document of an issuer, kept a day: its issuer must be the one asked for,
  // character for character (OpenID Connect Discovery 1.0, section 4.3).

  function padOAuthDiscover ( $issuer ) {

    if ( ! preg_match ( '~^https?://[^\s/?#]+[^\s?#]*$~iD', $issuer ) )
      return padOAuthAuthor ( "the issuer is the address of an OpenID provider, like https://id.example.com/realms/main - not '" . padMakeSafe ( $issuer, 60 ) . "'" );

    $key  = 'pad-oauth-discovery:' . $issuer;
    $fake = padCurlFaking ();
    $doc  = $fake ? NULL : padCacheGet ( $key );

    if ( ! is_array ( $doc ) ) {

      $answer = padOAuthFetch ( 'GET', rtrim ( $issuer, '/' ) . '/.well-known/openid-configuration', NULL, [ 'Accept' => 'application/json' ] );
      $doc    = $answer ['json'];

      if ( ! is_array ( $doc ) or $answer ['status'] != 200 )
        return padOAuthFail ( 'the OpenID configuration of ' . padMakeSafe ( $issuer, 80 ) . ' cannot be read (' . padOAuthProblem ( $answer ) . ')' );

      if ( ( $doc ['issuer'] ?? NULL ) !== $issuer )
        return padOAuthFail ( 'the OpenID configuration of ' . padMakeSafe ( $issuer, 80 ) . ' names another issuer' );

      if ( ! $fake )
        padCachePut ( $key, $doc, 86400 );

    }

    return $doc;

  }

  // One request to the provider: [ status, json, data, error ]. A form body goes
  // url-encoded, as the token endpoints want it.

  function padOAuthFetch ( $method, $url, $body, $headers, $basic = NULL ) {

    $input = [ 'url' => $url, 'headers' => $headers, 'options' => [ 'USERAGENT' => 'PAD' ] ];

    if ( $method == 'POST' ) {
      $input ['post'] = http_build_query ( (array) $body, '', '&', PHP_QUERY_RFC1738 );
      $input ['headers'] ['Content-Type'] = 'application/x-www-form-urlencoded';
    }

    // HTTP Basic as RFC 6749, section 2.3.1, has it: the id and the secret form-encoded
    // first. Written as the header, so a fake sees it as the provider would.

    if ( $basic )
      $input ['headers'] ['Authorization'] = 'Basic ' . base64_encode ( urlencode ( $basic [0] ) . ':' . urlencode ( $basic [1] ) );

    $out  = padCurl ( $input );
    $data = (string) ( $out ['data'] ?? '' );
    $json = json_decode ( $data, TRUE );

    // An old-style token endpoint answers form-encoded - GitHub's does without the Accept.

    if ( ! is_array ( $json ) and str_contains ( $data, '=' ) and ! str_contains ( $data, '<' ) ) {
      parse_str ( $data, $form );
      $json = ( isset ( $form ['access_token'] ) or isset ( $form ['error'] ) ) ? $form : NULL;
    }

    return [ 'status' => (int) ( $out ['result'] ?? 0 ), 'json' => is_array ( $json ) ? $json : NULL,
             'data'   => $data, 'error' => (string) ( $out ['ERROR'] ?? '' ) ];

  }

  // What a failed answer says - its OAuth error and description, else its status - as a
  // short text for padOAuthError.

  function padOAuthProblem ( $answer ) {

    $json = $answer ['json'] ?? NULL;

    if ( is_array ( $json ) and isset ( $json ['error'] ) ) {
      $error = is_array ( $json ['error'] ) ? ( $json ['error'] ['message'] ?? 'error' ) : $json ['error'];
      $about = padOAuthText ( $json ['error_description'] ?? '' );
      return padOAuthText ( $error ) . ( $about !== '' ? ": $about" : '' );
    }

    if ( $answer ['error'] !== '' )
      return 'no answer';

    return 'HTTP ' . $answer ['status'];

  }

  function padOAuthText ( $value ) {

    return is_scalar ( $value ) ? padMakeSafe ( trim ( (string) $value ), 200 ) : '';

  }

  function padOAuthFail ( $message ) {

    padOAuthStore ( 'error', $message );

    return FALSE;

  }

  // An author's mistake: named by padError under the strict check, and by padOAuthError
  // either way.

  function padOAuthAuthor ( $message ) {

    padOAuthStore ( 'error', $message );

    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( $message );

    return NULL;

  }

?>
