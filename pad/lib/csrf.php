<?php

  // Cross-site request forgery protection: a token bound to the visitor's session that a
  // form carries back, so a POST made from another site - which can make the browser send
  // the session cookie, but cannot read the page to learn the token - is told apart from
  // one made from this application's own form.
  //
  // padCsrfToken    the session's token, made on first use - the session starts on demand
  // padCsrfField    the hidden form field carrying it, what {csrf} writes
  // padCsrfValid    whether this request brought the session's token back, in the
  //                 padCsrfToken field or an X-CSRF-Token header (for a script's fetch)
  // padCsrfUnsafe   whether the request method changes state - anything but GET, HEAD and
  //                 OPTIONS - and so has to carry a token when $padCsrf is on
  // padCsrfForms    adds the field to every POST form of a finished page that posts back to
  //                 this site and does not hold one yet - exits/exits.php, with $padCsrf on
  //
  // The token is one per session rather than one per form: a visitor with two tabs open can
  // send either form, and the back button does not break a form that was valid. It is 32
  // random bytes, compared in constant time.
  //
  // Before this, an application had only the SameSite=Lax flag of its session cookie, which
  // a same-site page, an old browser, or a top-level GET that changes state goes around.

  const padCsrfName = 'padCsrfToken';

  function padCsrfToken () {

    global $padCsrfIssued;

    if ( ! padSessionStart () )
      return '';

    if ( ! is_string ( $_SESSION ['padCsrf'] ?? NULL ) or strlen ( $_SESSION ['padCsrf'] ) != 64 )
      $_SESSION ['padCsrf'] = bin2hex ( random_bytes ( 32 ) );

    // Remembered for the page cache, which must not store a page carrying it: the next
    // visitor would get this one's token (padCacheStorable).

    $padCsrfIssued = $_SESSION ['padCsrf'];

    return $padCsrfIssued;

  }

  function padCsrfField () {

    return '<input type="hidden" name="' . padCsrfName . '" value="' . padCsrfToken () . '">';

  }

  // No session cookie, no token to compare with: the session is not started for a check
  // that can only fail - starting it would send a cookie to a request being turned away.

  function padCsrfValid () {

    $sent = $_POST [padCsrfName] ?? $_SERVER ['HTTP_X_CSRF_TOKEN'] ?? '';

    if ( ! is_string ( $sent ) or $sent === '' )
      return FALSE;

    if ( ! padSessionExists () or ! padSessionStart () )
      return FALSE;

    $token = $_SESSION ['padCsrf'] ?? '';

    return is_string ( $token ) and $token !== '' and hash_equals ( $token, $sent );

  }

  function padCsrfUnsafe () {

    return ! padRequestIs ( [ 'GET', 'HEAD', 'OPTIONS' ] );

  }

  // Each <form> is looked at up to its </form> (or the next <form>): a form that posts, to
  // an address on this site, and does not already hold the field gets it right after its
  // opening tag. A GET form gets nothing - its fields end up in the URL, and a token there
  // leaks through history and Referer. Neither does a form posting to another site: the
  // token would be handed to it.
  //
  // The opening tags are found, and each form's end is looked for from its tag on, rather
  // than one regular expression taking in the content of every form: that ran out of
  // PCRE's backtrack limit on a form of about a megabyte - an admin list with a checkbox on
  // each of its thousands of rows - and answered NULL, which ended the page.

  function padCsrfForms ( $html ) {

    if ( stripos ( $html, '<form' ) === FALSE )
      return $html;

    preg_match_all ( '/<form\b[^>]*>/i', $html, $opens, PREG_OFFSET_CAPTURE );

    $out  = '';
    $done = 0;

    foreach ( $opens [0] as $index => [ $open, $at ] ) {

      $start = $at + strlen ( $open );
      $inner = substr ( $html, $start, ( $opens [0] [$index + 1] [1] ?? strlen ( $html ) ) - $start );

      if ( preg_match ( '/<\/form\s*>/i', $inner, $close, PREG_OFFSET_CAPTURE ) )
        $inner = substr ( $inner, 0, $close [0] [1] );

      if ( ! padCsrfFormPosts ( $open ) or str_contains ( $inner, 'name="' . padCsrfName . '"' ) )
        continue;

      $out .= substr ( $html, $done, $start - $done ) . padCsrfField ();

      $done = $start;

    }

    return $out . substr ( $html, $done );

  }

  function padCsrfFormPosts ( $open ) {

    // The attributes as a browser reads them, one after the other, the first of a name
    // counting: an action straight after a quote or a slash - method="post"action=... - is
    // the action, text in another attribute's value - title="see action=?here" - is none,
    // and the method is post exactly, any case. Each was looked for anywhere in the tag
    // after a space, which took the first two for forms posting here and handed them the
    // token, and gave it to method=" post" - a GET form, the token in its URL.

    preg_match_all ( '/([^\s"\'>\/=]+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+)))?/', substr ( $open, 5 ), $found, PREG_SET_ORDER );

    $attrs = [];

    foreach ( $found as $one )
      $attrs [ strtolower ( $one [1] ) ] ??= ( $one [2] ?? '' ) . ( $one [3] ?? '' ) . ( $one [4] ?? '' );

    if ( strtolower ( html_entity_decode ( $attrs ['method'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) !== 'post' )
      return FALSE;

    if ( ! array_key_exists ( 'action', $attrs ) )
      return TRUE;

    $action = html_entity_decode ( trim ( $attrs ['action'] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );

    // Read as a browser reads it (the URL standard): the controls and spaces at its ends
    // and every tab and line break within are dropped, and on a web page a backslash is a
    // slash - /\elsewhere.example/ and /&#9;/elsewhere.example/ post to that site, and
    // were handed the token as addresses on this one. The references are HTML5's: with
    // only those of HTML 4 decoded, /&Tab;/elsewhere.example/ and &sol;&sol;elsewhere.example/
    // were still read as paths here.

    $action = str_replace ( [ "\t", "\n", "\r", '\\' ], [ '', '', '', '/' ], trim ( $action, "\x00..\x20" ) );

    if ( ! preg_match ( '#^([a-z][a-z0-9+.-]*:)?//#i', $action ) )
      return ! preg_match ( '/^[a-z][a-z0-9+.-]*:/i', $action );

    return padCsrfSameOrigin ( $action );

  }

  // An absolute action is this site when its scheme, host and port are those of $padHost -
  // a protocol-relative //host/ takes the scheme of the page.

  function padCsrfSameOrigin ( $url ) {

    global $padHost;

    $own  = parse_url ( $padHost );
    $that = parse_url ( str_starts_with ( $url, '//' ) ? ( $own ['scheme'] ?? 'http' ) . ':' . $url : $url );

    if ( ! $own or ! $that )
      return FALSE;

    $port = fn ( $parts ) => $parts ['port'] ?? ( strtolower ( $parts ['scheme'] ?? '' ) == 'https' ? 443 : 80 );

    return strtolower ( $own  ['scheme'] ?? '' ) === strtolower ( $that ['scheme'] ?? '' )
       and strtolower ( $own  ['host']   ?? '' ) === strtolower ( $that ['host']   ?? '' )
       and $port ( $own ) == $port ( $that );

  }

?>
