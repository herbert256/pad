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

  // Each <form> is looked at up to its </form>: a form that posts, to an address on this
  // site, and does not already hold the field gets it right after its opening tag. A GET
  // form gets nothing - its fields end up in the URL, and a token there leaks through
  // history and Referer. Neither does a form posting to another site, or holding a button
  // that does: the token would be handed to it.
  //
  // The forms are found as a browser finds them, tag after tag (padCsrfWalk) - what stands
  // in a script, a style, a textarea or a comment is no tag, nor is the text of an attribute
  // value, and a form inside a form is none. Found by searching the text, a quote left open
  // in '<form title="' inside a script read on through the real form after it, which got no
  // token; a </form> in a value or a <form> in a comment ended a form early, hiding the
  // button that posts elsewhere; a form in a comment or a textarea got a token. Before that,
  // one regular expression over every form's content ran out of PCRE's backtrack limit on a
  // form of a megabyte and ended the page.

  //
  // A button belongs to the form its form= names, wherever it stands, and sends that form
  // where its formaction= says; a relative action is resolved against the page's <base
  // href>. A form one of whose buttons posts to another site gets no token - and loses the
  // one a {form} wrote: exits.php runs this pass, adding nothing, also with $padCsrf off
  // when a token went into the page. A button that sends the form by GET - formmethod=, in
  // any spelling a browser reads as GET - to this site leaves the token: it reaches this
  // site's own logs and history only, and a form with a Save and a Preview button could
  // not post at all without it.

  function padCsrfForms ( $html, $add = TRUE ) {

    if ( stripos ( $html, '<form' ) === FALSE )
      return $html;

    $forms    = [];
    $ids      = [];
    $controls = [];
    $base     = NULL;
    $open     = NULL;

    foreach ( padCsrfWalk ( $html ) as [ $kind, $name, $at, $end, $attrs ] )
      if ( $kind == 'start' and $name == 'form' and $open === NULL ) {
        $forms [] = [ 'at' => $at, 'start' => $end, 'end' => strlen ( $html ), 'attrs' => $attrs, 'controls' => [], 'token' => FALSE ];
        $open     = array_key_last ( $forms );
        if ( array_key_exists ( 'id', $attrs ) )
          $ids [ padCsrfDecode ( $attrs ['id'] ) ] ??= $open;
      } elseif ( $kind == 'end' and $name == 'form' and $open !== NULL ) {
        $forms [$open] ['end'] = $at;
        $open = NULL;
      } elseif ( $kind == 'start' and ( $name == 'button' or $name == 'input' ) )
        $controls [] = [ $attrs, $open ];
      elseif ( $kind == 'start' and $name == 'base' and $base === NULL and array_key_exists ( 'href', $attrs ) )
        $base = $attrs ['href'];

    foreach ( $controls as [ $attrs, $inside ] ) {

      $owner = array_key_exists ( 'form', $attrs ) ? ( $ids [ padCsrfDecode ( $attrs ['form'] ) ] ?? NULL ) : $inside;

      if ( $owner === NULL )
        continue;

      $forms [$owner] ['controls'] [] = $attrs;

      if ( ( $attrs ['name'] ?? '' ) === padCsrfName )
        $forms [$owner] ['token'] = TRUE;

    }

    $out  = '';
    $done = 0;

    foreach ( $forms as $form )

      if ( padCsrfFormAttrs ( $form ['attrs'], $base ) and padCsrfControlsAttrs ( $form ['controls'], $base ) ) {

        if ( $add and ! $form ['token'] ) {
          $out .= substr ( $html, $done, $form ['start'] - $done ) . padCsrfField ();
          $done = $form ['start'];
        }

      } elseif ( $form ['token'] and isset ( $GLOBALS ['padCsrfIssued'] ) ) {

        // Only the token a {form} wrote goes (lib/form.php keeps its opening text), never a
        // {csrf} the template wrote itself: that one was put there on purpose.

        $field = '<input type="hidden" name="' . padCsrfName . '" value="' . $GLOBALS ['padCsrfIssued'] . '">';

        foreach ( $GLOBALS ['padFormTokened'] ?? [] as $open )
          if ( substr ( $html, $form ['at'], strlen ( $open ) ) === $open ) {
            $out .= substr ( $html, $done, $form ['at'] - $done ) . str_replace ( $field, '', $open );
            $done = $form ['at'] + strlen ( $open );
            break;
          }

      }

    return $out . substr ( $html, $done );

  }

  // The tags of a page as a browser's tokenizer meets them: [ 'start' or 'end', name,
  // offset of the < , offset after the > , attributes ]. Comments and <! ... > / <? ... >
  // are passed over, and so is the content of the elements whose text holds no tags. A
  // comment ends as the tokenizer ends it - <!--> and <!---> are empty ones, and --!> ends
  // one as --> does - and inside an <svg> or a <math> a <style> or a <script> is an element
  // of its own, which the drawing's end closes: read on to the next --> or </style>, the
  // walk missed the button after them that posts the form to another site.

  const padCsrfRawText = [ 'script', 'style', 'textarea', 'title', 'xmp', 'iframe', 'noembed', 'noframes' ];

  function padCsrfWalk ( $html ) {

    $tags    = [];
    $at      = 0;
    $foreign = 0;

    while ( ( $at = strpos ( $html, '<', $at ) ) !== FALSE ) {

      $next = $html [$at + 1] ?? '';

      if ( $next == '!' and substr ( $html, $at, 4 ) == '<!--' ) {
        if ( substr ( $html, $at + 4, 1 ) == '>' or substr ( $html, $at + 4, 2 ) == '->' ) {
          $at = strpos ( $html, '>', $at + 4 ) + 1;
          continue;
        }
        $close = strpos ( $html, '-->', $at + 4 );
        $bang  = strpos ( $html, '--!>', $at + 4 );
        if ( $close === FALSE and $bang === FALSE )
          break;
        $at = ( $bang !== FALSE and ( $close === FALSE or $bang < $close ) ) ? $bang + 4 : $close + 3;
        continue;
      }

      if ( $next == '!' or $next == '?' ) {
        $close = strpos ( $html, '>', $at );
        if ( $close === FALSE )
          break;
        $at = $close + 1;
        continue;
      }

      if ( $next == '/' and ctype_alpha ( $html [$at + 2] ?? '' ) ) {
        $close = strpos ( $html, '>', $at );
        if ( $close === FALSE )
          break;
        $tags [] = [ 'end', strtolower ( substr ( $html, $at + 2, strcspn ( $html, " \t\n\f\r/>", $at + 2 ) ) ), $at, $close + 1, [] ];
        $at = $close + 1;
        if ( in_array ( end ( $tags ) [1], [ 'svg', 'math' ], TRUE ) and $foreign > 0 )
          $foreign--;
        continue;
      }

      if ( ! ctype_alpha ( $next ) ) {
        $at++;
        continue;
      }

      $tag = padCsrfTag ( $html, $at );

      if ( $tag === NULL )
        break;

      $name   = strtolower ( substr ( $html, $at + 1, strcspn ( $html, " \t\n\f\r/>", $at + 1 ) ) );
      $tags[] = [ 'start', $name, $at, $tag [0], $tag [1] ];
      $at     = $tag [0];

      if ( ( $name == 'svg' or $name == 'math' ) and $html [$tag [0] - 2] != '/' )
        $foreign++;

      if ( ! $foreign and in_array ( $name, padCsrfRawText, TRUE ) ) {
        if ( ! preg_match ( '/<\/' . $name . '[\s\/>]/i', $html, $close, PREG_OFFSET_CAPTURE, $at ) )
          break;
        $at = $close [0] [1];
      }

    }

    return $tags;

  }

  // The opening tag of a form - {form} asks too (lib/form.php) - and whether it posts here.

  function padCsrfFormPosts ( $open ) {

    $tag = padCsrfTag ( $open, 0 );

    return $tag !== NULL and padCsrfFormAttrs ( $tag [1] );

  }

  // A start tag read as the HTML standard's tokenizer reads it: past its name, the
  // attributes one after the other, the first of a name counting, names in lower case. A
  // name runs to a space, a / , a > or an = - a quote is part of it, and it may start with
  // an = - a value is quoted, or runs to a space or a > . [ the offset after the tag's > ,
  // [ name => value ] ], or NULL for a tag that never closes. Read with one regular
  // expression, x"action="?here" and =action="?here" were taken for the action, and a real
  // one to another site after them got the token.

  function padCsrfTag ( $html, $at ) {

    $space  = " \t\n\f\r";
    $length = strlen ( $html );
    $index  = $at + 1 + strcspn ( $html, "$space/>", $at + 1 );
    $attrs  = [];

    while ( TRUE ) {

      $index += strspn ( $html, "$space/", $index );

      if ( $index >= $length )
        return NULL;

      if ( $html [$index] == '>' )
        return [ $index + 1, $attrs ];

      $from   = $index;
      $index += 1 + strcspn ( $html, "$space/>=", $index + 1 );
      $name   = strtolower ( substr ( $html, $from, $index - $from ) );
      $index += strspn ( $html, $space, $index );
      $value  = '';

      if ( $index < $length and $html [$index] == '=' ) {

        $index += 1 + strspn ( $html, $space, $index + 1 );
        $quote  = $html [$index] ?? '';

        if ( $quote == '"' or $quote == "'" ) {

          $close = strpos ( $html, $quote, $index + 1 );

          if ( $close === FALSE )
            return NULL;

          $value = substr ( $html, $index + 1, $close - $index - 1 );
          $index = $close + 1;

        } elseif ( $quote != '>' ) {

          $from   = $index;
          $index += strcspn ( $html, "$space>", $index );
          $value  = substr ( $html, $from, $index - $from );

        }

      }

      $attrs [$name] ??= $value;

    }

  }

  // The method is post exactly, any case - method=" post" is a GET form to a browser, which
  // put the token into its URL - and the action, when there is one, is on this site.

  function padCsrfFormAttrs ( $attrs, $base = NULL ) {

    if ( strtolower ( padCsrfDecode ( $attrs ['method'] ?? '' ) ) !== 'post' )
      return FALSE;

    return ! array_key_exists ( 'action', $attrs ) or padCsrfActionHere ( $attrs ['action'], $base );

  }

  // A submit button's formaction= sends the form where it says: a form holding one that
  // points to another site handed the token along with every click on that button.

  function padCsrfControlsHere ( $html ) {

    if ( stripos ( $html, 'formaction' ) === FALSE )
      return TRUE;

    $controls = [];

    foreach ( padCsrfWalk ( $html ) as [ $kind, $name, , , $attrs ] )
      if ( $kind == 'start' and ( $name == 'button' or $name == 'input' ) )
        $controls [] = $attrs;

    return padCsrfControlsAttrs ( $controls );

  }

  function padCsrfControlsAttrs ( $controls, $base = NULL ) {

    foreach ( $controls as $attrs )
      if ( array_key_exists ( 'formaction', $attrs ) and ! padCsrfActionHere ( $attrs ['formaction'], $base ) )
        return FALSE;

    return TRUE;

  }

  // An action - or a formaction - on this site; a relative one under a <base href> is where
  // the base is, and an empty one the page's own address.

  function padCsrfActionHere ( $action, $base = NULL ) {

    $action = padCsrfDecode ( $action );

    // Read as a browser reads it (the URL standard): the controls and spaces at its ends
    // and every tab and line break within are dropped, and on a web page a backslash is a
    // slash - /\elsewhere.example/ and /&#9;/elsewhere.example/ post to that site, and
    // were handed the token as addresses on this one. The references are HTML5's: with
    // only those of HTML 4 decoded, /&Tab;/elsewhere.example/ and &sol;&sol;elsewhere.example/
    // were still read as paths here.

    $action = str_replace ( [ "\t", "\n", "\r", '\\' ], [ '', '', '', '/' ], trim ( $action, "\x00..\x20" ) );

    if ( $action !== '' and $base !== NULL and ! preg_match ( '/^[a-z][a-z0-9+.-]*:/i', $action ) and ! str_starts_with ( $action, '//' ) )
      return padCsrfActionHere ( $base );

    if ( ! preg_match ( '#^([a-z][a-z0-9+.-]*:)?//#i', $action ) )
      return ! preg_match ( '/^[a-z][a-z0-9+.-]*:/i', $action );

    return padCsrfSameOrigin ( $action );

  }

  // The character references of an attribute value decoded as a browser decodes them, in
  // one pass: a numeric one to any code point, its semicolon optional - &#13; a carriage
  // return, &#47 a slash, &#1; a control - and a named one as HTML5 names it. PHP's HTML5
  // decoding leaves &#13; and &#1; as they are and wants the semicolon, so
  // /&#13;/elsewhere.example/ and &#47&#47elsewhere.example/ were read as paths here.

  function padCsrfDecode ( $value ) {

    return preg_replace_callback ( '/&(?:#[xX]([0-9a-fA-F]+);?|#([0-9]+);?|[A-Za-z][A-Za-z0-9]*;)/', function ( $match ) {

      if ( ( $match [1] ?? '' ) === '' and ( $match [2] ?? '' ) === '' )
        return html_entity_decode ( $match [0], ENT_QUOTES | ENT_HTML5, 'UTF-8' );

      $code = ( $match [1] !== '' ) ? hexdec ( $match [1] ) : (float) $match [2];

      return ( $code > 0 and $code <= 0x10FFFF and ( $code < 0xD800 or $code > 0xDFFF ) ) ? mb_chr ( (int) $code, 'UTF-8' ) : "\u{FFFD}";

    }, $value );

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
