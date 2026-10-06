<?php

  // Request and session helpers: what a page's PHP asks of the request it answers, of the
  // session that outlives it, and of the address the visitor goes to next. The engine turns
  // request values into variables ($padRequestVars) and keeps $padSessionVars names in the
  // session; these read and write the same things by name, from PHP, whatever those two
  // settings let through - the way Laravel's request(), session(), old(), url(), back() and
  // abort() do.
  //
  // padRequest            one value of this request's input by name or dot path - POST over
  //                       GET, and the decoded body of a JSON request - or all of it
  // padRequestHas         whether every given name is in the input (an empty value counts)
  // padRequestFilled      whether every given name is in the input and not blank
  // padRequestOnly        the input with the given names only
  // padRequestExcept      the input without the given names
  // padRequestMethod      the request method, upper case - GET on the command line
  // padRequestIs          whether the method is the one given, or one of a list
  // padSession            one value of the session by name or dot path, or all of it -
  //                       reading never starts a session the visitor did not bring
  // padSessionPut         keeps a value in the session, starting it on demand
  // padSessionHas         whether every given name is in the session
  // padSessionPull        a value of the session, taken out of it
  // padSessionForget      removes one or several names from the session
  // padSessionRegenerate  gives the session a new id, its data kept - after a login
  // padFlashInput         keeps this request's input for the next request only
  // padOld                reads that input there - the form refilled after a redirect
  // padUrl                a link to a page of this application, with values
  // padBack               redirects to the page the visitor came from, never off-site
  // padAbort              ends the request with an error status and a message
  //
  // The private helpers below them: padRequestInput and padRequestJson gather the input,
  // padRequestKey and padRequestKeys read the names a function is given, padRequestFind,
  // padRequestDotSet and padRequestDotForget walk a dot path, padRequestBlank says what is
  // blank, padSessionOpen, padSessionView and padSessionWrite keep the session in step with
  // the $padSessionVars globals, padOldTake (inits/flash.php) and padOldCookie age the
  // flashed input, padBackTarget checks the referring address and padAbortPhrase names a
  // status.
  //
  // Dot paths: 'user.address.city' reads $input ['user'] ['address'] ['city']; a name that
  // is a key of its own - dots and all - is found first; a segment may be a number; a *
  // segment maps over every item of its level and answers a list. Objects are read by their
  // public properties, ArrayAccess objects like arrays.

  const padOldCookie  = 'padOld';
  const padOldSession = 'padOldInput';

  const padAbortPhrases = [
    400 => 'Bad Request',                     401 => 'Unauthorized',
    402 => 'Payment Required',                403 => 'Forbidden',
    404 => 'Not Found',                       405 => 'Method Not Allowed',
    406 => 'Not Acceptable',                  407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',                 409 => 'Conflict',
    410 => 'Gone',                            411 => 'Length Required',
    412 => 'Precondition Failed',             413 => 'Content Too Large',
    414 => 'URI Too Long',                    415 => 'Unsupported Media Type',
    416 => 'Range Not Satisfiable',           417 => 'Expectation Failed',
    418 => "I'm a teapot",                    421 => 'Misdirected Request',
    422 => 'Unprocessable Content',           423 => 'Locked',
    424 => 'Failed Dependency',               425 => 'Too Early',
    426 => 'Upgrade Required',                428 => 'Precondition Required',
    429 => 'Too Many Requests',               431 => 'Request Header Fields Too Large',
    451 => 'Unavailable For Legal Reasons',   500 => 'Internal Server Error',
    501 => 'Not Implemented',                 502 => 'Bad Gateway',
    503 => 'Service Unavailable',             504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',      506 => 'Variant Also Negotiates',
    507 => 'Insufficient Storage',            508 => 'Loop Detected',
    510 => 'Not Extended',                    511 => 'Network Authentication Required'
  ];

  // One value of the input, or all of it. The page's PHP sees a request value as a
  // variable only when $padRequestVars lets it through, and then under a name that can be
  // shadowed by anything set before; this asks the request itself, by the name the form
  // used, and a missing value answers the default instead of an undefined variable.

  function padRequest ( $key = NULL, $default = NULL ) {

    $input = padRequestInput ();

    if ( $key === NULL )
      return $input;

    $key = padRequestKey ( $key, 'padRequest' );

    if ( $key === NULL )
      return padRequestDefault ( $default );

    [ $found, $value ] = padRequestFind ( $input, $key );

    return $found ? $value : padRequestDefault ( $default );

  }

  // An empty value is still a value the form sent - an emptied field says something - so
  // has() is about presence, and filled() about content: both ask for every name given.

  function padRequestHas ( $keys ) {

    $keys  = padRequestKeys ( $keys, 'padRequestHas' );
    $input = padRequestInput ();

    foreach ( $keys as $key )
      if ( ! padRequestFind ( $input, $key ) [0] )
        return FALSE;

    return count ( $keys ) > 0;

  }

  // Filled: sent, and something in it - not blank (padRequestBlank).

  function padRequestFilled ( $key ) {

    $keys  = padRequestKeys ( $key, 'padRequestFilled' );
    $input = padRequestInput ();

    foreach ( $keys as $one ) {

      [ $found, $value ] = padRequestFind ( $input, $one );

      if ( ! $found or padRequestBlank ( $value ) )
        return FALSE;

    }

    return count ( $keys ) > 0;

  }

  // The names asked for, in the order asked, each where its dot path puts it - a name the
  // request did not bring is left out rather than made up, so what comes back can be saved
  // as it is.

  function padRequestOnly ( $keys ) {

    $input  = padRequestInput ();
    $result = [];

    foreach ( padRequestKeys ( $keys, 'padRequestOnly' ) as $key ) {

      [ $found, $value ] = padRequestFind ( $input, $key );

      if ( $found )
        padRequestDotSet ( $result, $key, $value, $input );

    }

    return $result;

  }

  // Everything but the names given - the input to save with the password and the CSRF
  // token taken out, the way padFlashInput keeps it.

  function padRequestExcept ( $keys ) {

    $input = padRequestInput ();

    foreach ( padRequestKeys ( $keys, 'padRequestExcept' ) as $key )
      padRequestDotForget ( $input, $key );

    return $input;

  }

  // A command-line run has no method at all; it renders the way a GET does. Any method
  // HTTP allows - a token, M-SEARCH as much as POST - is answered as it came, upper-cased:
  // the engine asks here too, and its CSRF check must see an unusual method as the
  // unsafe one it is, not as a GET.

  function padRequestMethod () {

    $method = strtoupper ( (string) ( $_SERVER ['REQUEST_METHOD'] ?? '' ) );

    return preg_match ( "/^[A-Z0-9!#$%&'*+.^_`|~-]+$/D", $method ) ? $method : 'GET';

  }

  // One method or any of a list - an array, or a text with commas - in any case: a page
  // that answers PUT and PATCH alike asks once.

  function padRequestIs ( $method ) {

    if ( is_string ( $method ) )
      $method = explode ( ',', $method );

    if ( ! is_array ( $method ) ) {
      padError ( 'padRequestIs: the method must be a name like POST or a list of them, not ' . get_debug_type ( $method ) );
      return FALSE;
    }

    $now = padRequestMethod ();

    foreach ( $method as $one )
      if ( is_string ( $one ) and strtoupper ( trim ( $one ) ) === $now )
        return TRUE;

    return FALSE;

  }

  // Reading needs a session the visitor brought along - its cookie - and resumes that one;
  // without it the answer is the default, and no session, no cookie and no session file
  // are made just to find out that there is nothing.

  function padSession ( $key = NULL, $default = NULL ) {

    if ( $key !== NULL ) {
      $key = padRequestKey ( $key, 'padSession' );
      if ( $key === NULL )
        return padRequestDefault ( $default );
    }

    if ( ! padSessionOpen ( FALSE ) )
      return ( $key === NULL ) ? [] : padRequestDefault ( $default );

    $view = padSessionView ();

    if ( $key === NULL )
      return $view;

    [ $found, $value ] = padRequestFind ( $view, $key );

    return $found ? $value : padRequestDefault ( $default );

  }

  // Writing starts the session when there is none yet. An array of names and values puts
  // them all. FALSE when no session can start - the headers have gone out, after {flush}.

  function padSessionPut ( $key, $value = NULL ) {

    if ( is_array ( $key ) ) {

      $done = TRUE;

      foreach ( $key as $name => $one )
        $done = padSessionPut ( (string) $name, $one ) && $done;

      return $done;

    }

    $key = padRequestKey ( $key, 'padSessionPut' );

    if ( $key === NULL or $key === '' )
      return padError ( 'padSessionPut: the name of the value to keep is missing' );

    // A value PHP cannot serialize - a Closure, an anonymous class - makes the write at the
    // end of the request fail, and with it every other value of the session; it is refused
    // here, where the author can see which one it was.

    try {
      serialize ( $value );
    } catch ( Throwable $e ) {
      return padError ( "padSessionPut: the value of '$key' cannot be kept in a session - " . $e->getMessage () );
    }

    if ( ! padSessionOpen ( TRUE ) )
      return FALSE;

    $view = padSessionView ();
    $top  = padRequestDotSet ( $view, $key, $value, $view );

    // PHP's session drops a top-level key that is a number when it writes the session -
    // silently, the value is simply not there on the next request.

    if ( is_int ( array_key_first ( [ $top => TRUE ] ) ) )
      return padError ( "padSessionPut: a session name cannot be a number ('$top') - PHP's session does not keep it" );

    // A | in a top-level name is worse: PHP's session refuses to write the session at all,
    // and every value of it - the login among them - is gone after the request, silently.

    if ( str_contains ( (string) $top, '|' ) )
      return padError ( "padSessionPut: a session name cannot hold a | ('$top') - PHP's session would not be written at all" );

    padSessionWrite ( $view, $top );

    return TRUE;

  }

  // Presence, as padRequestHas: a name kept with a NULL value is there.

  function padSessionHas ( $keys ) {

    $keys = padRequestKeys ( $keys, 'padSessionHas' );

    if ( ! count ( $keys ) or ! padSessionOpen ( FALSE ) )
      return FALSE;

    $view = padSessionView ();

    foreach ( $keys as $key )
      if ( ! padRequestFind ( $view, $key ) [0] )
        return FALSE;

    return TRUE;

  }

  // A value that is used once - a coupon, a one-time code - read and taken out in one step,
  // so a second request cannot use it again. Like a read, it never starts a session.

  function padSessionPull ( $key, $default = NULL ) {

    if ( $key === NULL ) {
      padError ( 'padSessionPull: the name of the value to take is missing' );
      return padRequestDefault ( $default );
    }

    $key = padRequestKey ( $key, 'padSessionPull' );

    if ( $key === NULL )
      return padRequestDefault ( $default );

    if ( ! padSessionOpen ( FALSE ) )
      return padRequestDefault ( $default );

    [ $found, $value ] = padRequestFind ( padSessionView (), $key );

    if ( ! $found )
      return padRequestDefault ( $default );

    padSessionForget ( [ $key ] );

    return $value;

  }

  // One name or dot path, or several - a logout forgets the user and the cart at once. A
  // session that is not there has nothing to forget and is not started for it.

  function padSessionForget ( $keys ) {

    $keys = padRequestKeys ( $keys, 'padSessionForget' );

    if ( ! count ( $keys ) or ! padSessionOpen ( FALSE ) )
      return;

    foreach ( $keys as $key ) {

      $view = padSessionView ();
      $top  = padRequestDotForget ( $view, $key );

      if ( $top !== NULL )
        padSessionWrite ( $view, $top );

    }

  }

  // A login moves the visitor to a new session id, so an id someone learned or planted
  // before it is worth nothing after it - session fixation. The data moves along and the
  // old session is deleted. The CSRF token does not move along: one planted with the
  // session was known to whoever planted it, and kept the forms of the logged-in visitor
  // open to them; the next form gets a new one (padCsrfToken).

  function padSessionRegenerate () {

    if ( headers_sent () or ! padSessionOpen ( TRUE ) )
      return FALSE;

    if ( ! session_regenerate_id ( TRUE ) )
      return FALSE;

    unset ( $_SESSION ['padCsrf'] );

    return TRUE;

  }

  // The redirect-after-a-failed-post pattern: the page that took the post keeps what was
  // sent and sends the browser back to the form, which fills its fields from padOld. The
  // input lives exactly one request after this one, the way a flash message does
  // (lib/flash.php): the padOld cookie tells the next request to take it out of the
  // session, and it is gone after that whether a page read it or not - a form opened later
  // shows no stale values. Passwords and the CSRF token are left out by default.
  //
  // This request's own padOld reads the input just flashed, as the next one will.

  function padFlashInput ( $except = [ 'password', 'password_confirmation', 'padCsrfToken' ] ) {

    $input = padRequestExcept ( $except );

    $GLOBALS ['padOldFresh'] = $input;

    if ( ! count ( $input ) ) {
      if ( session_status () === PHP_SESSION_ACTIVE )
        unset ( $_SESSION [padOldSession] );
      return TRUE;
    }

    if ( ! padSessionStart () )
      return FALSE;

    $_SESSION [padOldSession] = $input;

    padOldCookie ( TRUE );

    return TRUE;

  }

  // The flashed input by name or dot path - what this request flashed when it did, else what
  // the request before it flashed (padOldTake) - so a form refills a field with
  // value=$email; '' by default, as an empty field.

  function padOld ( $key = NULL, $default = '' ) {

    $old = $GLOBALS ['padOldFresh'] ?? $GLOBALS ['padOldNow'] ?? [];

    if ( $key === NULL )
      return $old;

    $key = padRequestKey ( $key, 'padOld' );

    if ( $key === NULL )
      return padRequestDefault ( $default );

    [ $found, $value ] = padRequestFind ( $old, $key );

    return $found ? $value : padRequestDefault ( $default );

  }

  // A link built from the setting rather than written by hand: $padCleanUrls decides the
  // form at the moment of the call - /shop/products/42 or /shop/?products/42 - as it does
  // for $padGo. The page is the one the visitor asked for when none is named; a page written
  // the way a link writes it, ?about or about&x=1, is taken as that. Values are encoded as a
  // query - arrays too - and a #fragment stays at the end.

  function padUrl ( $page = '', $vars = [], $absolute = FALSE ) {

    global $padApp, $padCleanUrls, $padHost, $padPage, $padPageAsked, $padRoot;

    if ( $page === NULL or $page === FALSE )
      $page = '';

    if ( ! is_string ( $page ) and ! is_int ( $page ) ) {
      padError ( 'padUrl: the page must be a page name, not ' . get_debug_type ( $page ) );
      $page = '';
    }

    $page = (string) $page;
    $hash = '';

    if ( ( $at = strpos ( $page, '#' ) ) !== FALSE ) {
      $hash = substr ( $page, $at );
      $page = substr ( $page, 0, $at );
    }

    $page  = ltrim ( $page, '?' );
    $extra = '';

    if ( ( $at = strcspn ( $page, '?&' ) ) < strlen ( $page ) ) {
      $extra = substr ( $page, $at + 1 );
      $page  = substr ( $page, 0, $at );
    }

    $page = trim ( $page, '/' );

    if ( $page === '' )
      $page = ( $padPageAsked ?? '' ) !== '' ? $padPageAsked : ( $padPage ?? 'index' );

    $page = implode ( '/', array_map ( 'rawurlencode', explode ( '/', $page ) ) );

    if     ( is_array ( $vars ) or is_object ( $vars ) ) $query = http_build_query ( $vars );
    elseif ( $vars === NULL                            ) $query = '';
    elseif ( is_string ( $vars )                       ) $query = ltrim ( $vars, '?&' );
    else {
      padError ( 'padUrl: the values must be an array, not ' . get_debug_type ( $vars ) );
      $query = '';
    }

    $query = implode ( '&', array_filter ( [ $extra, $query ], 'strlen' ) );
    $url   = ( $absolute ? $padHost : ( $padRoot ?? '/' ) ) . "$padApp/" . ( $padCleanUrls ? '' : '?' ) . $page;

    if ( $query !== '' )
      $url .= ( str_contains ( $url, '?' ) ? '&' : '?' ) . $query;

    return $url . $hash;

  }

  // The Referer header is the visitor's to write, so it is only followed when it names a
  // page of this application on this host - anything else, or none, goes to the fallback.
  // The address is rebuilt on $padHost rather than sent back as it came, so not even a
  // header with a line break or a scheme of its own reaches the Location.

  function padBack ( $fallback = '' ) {

    $target = padBackTarget ();

    if ( $target !== '' ) {
      padHeader ( 'Location: ' . padAddIds ( $target ) );
      padExit ( 302 );
    }

    if ( ! is_string ( $fallback ) and ! is_int ( $fallback ) and $fallback !== NULL ) {
      padError ( 'padBack: the fallback must be a page name, not ' . get_debug_type ( $fallback ) );
      $fallback = '';
    }

    $fallback = ltrim ( (string) $fallback, '?' );

    padRedirect ( $fallback === '' ? 'index' : $fallback );

  }

  // A status the request cannot go on from: the visitor gets it with the message - or the
  // status's own phrase - as plain text, through the engine's exit for a refused request
  // (padRefuse, lib/exit.php), so the session is written and closed and nothing the page
  // had made so far is sent. Only an error status: a 200 or a redirect is no abort.

  function padAbort ( $status, $message = '' ) {

    $code = ( is_int ( $status ) or ( is_string ( $status ) and ctype_digit ( $status ) ) ) ? (int) $status : 0;

    if ( $code < 400 or $code > 599 ) {
      padError ( 'padAbort: the status must be an HTTP error code from 400 to 599, not ' . padRequestShow ( $status ) );
      return NULL;
    }

    if ( $message !== NULL and ! is_scalar ( $message ) and ! $message instanceof Stringable ) {
      padError ( 'padAbort: the message must be a text, not ' . get_debug_type ( $message ) );
      $message = '';
    }

    $text = ( $message === NULL or (string) $message === '' ) ? padAbortPhrase ( $code ) : (string) $message;

    // The $padSessionVars names are written back once the request has taken them in - the
    // hand-over to the application (inits/app.php) defines APP2; before it, writing them
    // back would empty them, so the session is only closed as it stands.

    if ( defined ( 'APP2' ) )
      padCloseSession ();

    padRefuse ( $code, $text );

  }

  // ------------------------------------------------------------------------------------
  // The private helpers.
  // ------------------------------------------------------------------------------------

  // The input of this request: the JSON body over POST over GET, as Laravel merges them,
  // the bare query key that names the page left out ($padPageKey, inits/page.php), every
  // text trimmed as inits/parms.php trims a request value.

  function padRequestInput () {

    $get = is_array ( $_GET ?? NULL ) ? $_GET : [];

    $pageKey = (string) ( $GLOBALS ['padPageKey'] ?? '' );

    if ( $pageKey !== '' and ( $get [$pageKey] ?? NULL ) === '' )
      unset ( $get [$pageKey] );

    $post = is_array ( $_POST ?? NULL ) ? $_POST : [];

    return padGetParms2 ( 'POST', padRequestJson () + $post + $get );

  }

  // A body sent as application/json - or a +json type, application/vnd.api+json - never
  // reaches $_POST; it is read and decoded once per request. A body that is no JSON object
  // or array is no input.

  function padRequestJson () {

    static $json = NULL;

    if ( $json !== NULL )
      return $json;

    $json = [];
    $type = (string) ( $_SERVER ['CONTENT_TYPE'] ?? $_SERVER ['HTTP_CONTENT_TYPE'] ?? '' );

    if ( ! preg_match ( '#^\s*application/([a-z0-9.+-]*\+)?json\s*(;|$)#i', $type ) )
      return $json;

    $body    = (string) file_get_contents ( 'php://input' );
    $decoded = ( $body === '' ) ? NULL : json_decode ( $body, TRUE );

    if ( is_array ( $decoded ) )
      $json = $decoded;

    return $json;

  }

  // One name: a text or a number. Anything else is the author's mistake, said by name.

  function padRequestKey ( $key, $function ) {

    if ( is_string ( $key ) or is_int ( $key ) )
      return (string) $key;

    padError ( "$function: a name must be a text or a number, not " . get_debug_type ( $key ) );

    return NULL;

  }

  // Several names: an array of them, or one text with commas between them.

  function padRequestKeys ( $keys, $function ) {

    if ( $keys === NULL )
      return [];

    if ( is_int ( $keys ) )
      return [ (string) $keys ];

    if ( is_string ( $keys ) )
      return array_values ( array_filter ( array_map ( 'trim', explode ( ',', $keys ) ), 'strlen' ) );

    if ( ! is_array ( $keys ) ) {
      padError ( "$function: the names must be an array or a comma-separated text, not " . get_debug_type ( $keys ) );
      return [];
    }

    $list = [];

    foreach ( $keys as $key )
      if ( is_string ( $key ) or is_int ( $key ) )
        $list [] = (string) $key;
      else
        padError ( "$function: a name must be a text or a number, not " . get_debug_type ( $key ) );

    return $list;

  }

  // A Closure default is called only when the default is needed, so an expensive one - a
  // database lookup - costs nothing when the value was there.

  function padRequestDefault ( $default ) {

    return ( $default instanceof Closure ) ? $default () : $default;

  }

  // A wrong argument as an error message shows it: the value itself when it is a scalar,
  // its type otherwise.

  function padRequestShow ( $value ) {

    return is_scalar ( $value ) ? var_export ( $value, TRUE ) : get_debug_type ( $value );

  }

  // [ found, value ] for a name or a dot path: the name as a key of its own first, then
  // the path one segment at a time.

  function padRequestFind ( $target, $key ) {

    if ( padRequestStep ( $target, $key, $value ) )
      return [ TRUE, $value ];

    if ( ! str_contains ( $key, '.' ) )
      return [ FALSE, NULL ];

    return padRequestWalk ( $target, explode ( '.', $key ) );

  }

  // A * maps the rest of the path over every item of its level and answers the items that
  // have it, as a list; a second * further on flattens into the same list.

  function padRequestWalk ( $target, $segments ) {

    while ( count ( $segments ) ) {

      $segment = array_shift ( $segments );

      if ( $segment === '*' ) {

        if ( ! is_array ( $target ) and ! is_object ( $target ) )
          return [ FALSE, NULL ];

        $list = [];
        $more = in_array ( '*', $segments, TRUE );

        foreach ( ( $target instanceof Traversable or is_array ( $target ) ) ? $target : get_object_vars ( $target ) as $item ) {

          [ $found, $value ] = count ( $segments ) ? padRequestWalk ( $item, $segments ) : [ TRUE, $item ];

          if ( ! $found )
            continue;

          if ( $more and is_array ( $value ) )
            array_push ( $list, ...array_values ( $value ) );
          else
            $list [] = $value;

        }

        return [ TRUE, $list ];

      }

      if ( ! padRequestStep ( $target, $segment, $target ) )
        return [ FALSE, NULL ];

    }

    return [ TRUE, $target ];

  }

  // One segment of a path: a key of an array, an offset of an ArrayAccess object, a public
  // property of any other object. TRUE and the value in $value when it is there - a NULL
  // value is there.

  function padRequestStep ( $target, $segment, &$value ) {

    if ( is_array ( $target ) ) {

      if ( ! array_key_exists ( $segment, $target ) )
        return FALSE;

      $value = $target [$segment];
      return TRUE;

    }

    if ( $target instanceof ArrayAccess ) {

      if ( ! $target->offsetExists ( $segment ) )
        return FALSE;

      $value = $target [$segment];
      return TRUE;

    }

    if ( is_object ( $target ) ) {

      $vars = get_object_vars ( $target );

      if ( ! array_key_exists ( $segment, $vars ) )
        return FALSE;

      $value = $vars [$segment];
      return TRUE;

    }

    return FALSE;

  }

  // Sets a value at a dot path, making the levels on the way, and answers the top-level
  // key it went under. A name that is a key of its own in $like - the data it was read
  // from - is that key, so what padRequestFind reads back is what was written.

  function padRequestDotSet ( &$array, $key, $value, $like ) {

    if ( ! is_array ( $array ) )
      $array = [];

    if ( ! str_contains ( $key, '.' ) or ( is_array ( $like ) and array_key_exists ( $key, $like ) ) ) {
      $array [$key] = $value;
      return $key;
    }

    $segments = explode ( '.', $key );
    $last     = array_pop ( $segments );
    $here     = &$array;

    foreach ( $segments as $segment ) {
      if ( ! isset ( $here [$segment] ) or ! is_array ( $here [$segment] ) )
        $here [$segment] = [];
      $here = &$here [$segment];
    }

    $here [$last] = $value;

    return $segments [0];

  }

  // Removes a dot path, and answers the top-level key it changed - NULL when there was
  // nothing to remove.

  function padRequestDotForget ( &$array, $key ) {

    if ( ! is_array ( $array ) )
      return NULL;

    if ( array_key_exists ( $key, $array ) ) {
      unset ( $array [$key] );
      return $key;
    }

    $segments = explode ( '.', $key );
    $last     = array_pop ( $segments );
    $here     = &$array;

    if ( ! count ( $segments ) )
      return NULL;

    foreach ( $segments as $segment ) {
      if ( ! isset ( $here [$segment] ) or ! is_array ( $here [$segment] ) )
        return NULL;
      $here = &$here [$segment];
    }

    if ( ! array_key_exists ( $last, $here ) )
      return NULL;

    unset ( $here [$last] );

    return $segments [0];

  }

  // Blank: nothing, an empty or whitespace-only text, an empty array or Countable. A '0',
  // a 0 and a FALSE are answers.

  function padRequestBlank ( $value ) {

    if ( $value === NULL                         ) return TRUE;
    if ( is_string ( $value )                    ) return trim ( $value ) === '';
    if ( is_array ( $value )                     ) return count ( $value ) == 0;
    if ( $value instanceof Countable             ) return count ( $value ) == 0;

    return FALSE;

  }

  // A session to read or write: one already open, or the visitor's resumed - and only for
  // a write, a new one.

  function padSessionOpen ( $start ) {

    if ( session_status () === PHP_SESSION_ACTIVE )
      return TRUE;

    if ( ! $start and ! padSessionExists () )
      return FALSE;

    return padSessionStart ();

  }

  // The session as the end of the request will leave it. A $padSessionVars name lives in
  // its variable while the page runs, and padCloseSession writes that variable back over
  // the session at the end - so once the request has taken those names in (inits/parms.php,
  // then APP2 in inits/app.php), the variable is what the session holds: set, it is the
  // value; unset or NULL, the name is gone.

  function padSessionView () {

    $view = is_array ( $_SESSION ?? NULL ) ? $_SESSION : [];

    if ( ! defined ( 'APP2' ) )
      return $view;

    foreach ( $GLOBALS ['padSessionVars'] ?? [] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $view [$var] = $GLOBALS [$var];
      else
        unset ( $view [$var] );

    return $view;

  }

  // One top-level name written back to the session - and to its variable when it is a
  // $padSessionVars name, or the end of the request would write the old value back over
  // the new one. A name taken out leaves its variable NULL, which the end of the request
  // removes and a template still reads as empty.

  function padSessionWrite ( $view, $top ) {

    $declared = ( defined ( 'APP2' ) and in_array ( $top, $GLOBALS ['padSessionVars'] ?? [], TRUE ) );

    if ( array_key_exists ( $top, $view ) ) {
      $_SESSION [$top] = $view [$top];
      if ( $declared )
        $GLOBALS [$top] = $view [$top];
    } else {
      unset ( $_SESSION [$top] );
      if ( $declared )
        $GLOBALS [$top] = NULL;
    }

  }

  // inits/flash.php: a request bringing the padOld cookie takes the input the request
  // before it flashed out of the session, for padOld to read during this request only.

  function padOldTake () {

    global $padOldNow;

    $padOldNow = [];

    if ( ! isset ( $_COOKIE [padOldCookie] ) )
      return;

    if ( ! padSessionExists () or ! padSessionStart () ) {
      padOldCookie ( FALSE );
      return;
    }

    $padOldNow = is_array ( $_SESSION [padOldSession] ?? NULL ) ? $_SESSION [padOldSession] : [];

    unset ( $_SESSION [padOldSession] );

    padOldCookie ( FALSE );

  }

  // The sign for the next request, on the session cookie's path like padFlash's.

  function padOldCookie ( $set ) {

    if ( headers_sent () )
      return;

    if ( ( $GLOBALS ['padOldSign'] ?? NULL ) === $set )
      return;

    if ( ! $set and ! isset ( $_COOKIE [padOldCookie] ) and ! isset ( $GLOBALS ['padOldSign'] ) )
      return;

    $GLOBALS ['padOldSign'] = $set;

    setcookie ( padOldCookie, $set ? '1' : '', [
      'expires'  => $set ? 0 : 1,
      'path'     => ini_get ( 'session.cookie_path' ) ?: '/',
      'httponly' => TRUE,
      'samesite' => 'Lax',
      'secure'   => ( $_SERVER ['HTTPS'] ?? '' ) !== '' and ( $_SERVER ['HTTPS'] ?? '' ) !== 'off'
    ] );

  }

  // The referring address, when it is a page of this application on this host: the same
  // host and port as $padHost, a path under this application's own directory - /pad/shop/
  // and not /pad/shop2/ - and no control character anywhere. '' otherwise.

  function padBackTarget () {

    global $padApp, $padHost;

    $referer = $_SERVER ['HTTP_REFERER'] ?? '';

    if ( ! is_string ( $referer ) or $referer === '' or preg_match ( '/[\x00-\x20\x7f\\\\]/', $referer ) )
      return '';

    $from = parse_url ( $referer );
    $base = parse_url ( (string) $padHost );

    if ( ! is_array ( $from ) or ! is_array ( $base ) )
      return '';

    $scheme = strtolower ( $from ['scheme'] ?? '' );

    if ( ! in_array ( $scheme, [ 'http', 'https' ], TRUE ) or isset ( $from ['user'] ) )
      return '';

    $fromPort = $from ['port'] ?? ( $scheme == 'https' ? 443 : 80 );
    $basePort = $base ['port'] ?? ( strtolower ( $base ['scheme'] ?? '' ) == 'https' ? 443 : 80 );

    if ( strtolower ( $from ['host'] ?? '' ) !== strtolower ( $base ['host'] ?? '' ) or $fromPort != $basePort )
      return '';

    $prefix = rtrim ( $base ['path'] ?? '/', '/' ) . "/$padApp/";
    $path   = $from ['path'] ?? '';

    if ( ! str_starts_with ( $path, $prefix ) )
      return '';

    return $padHost . "$padApp/" . substr ( $path, strlen ( $prefix ) )
         . ( isset ( $from ['query'] ) ? '?' . $from ['query'] : '' );

  }

  // The reason phrase of a status, from the HTTP specification; one it does not name gets
  // the phrase of its class.

  function padAbortPhrase ( $code ) {

    return padAbortPhrases [$code] ?? ( $code < 500 ? 'Client Error' : 'Server Error' );

  }

?>
