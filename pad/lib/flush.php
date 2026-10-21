<?php

  // Early flush: {flush} sends the page as far as it is rendered, so the browser fetches the
  // stylesheets and fonts the <head> names while the rest of the page - a slow {table}, a
  // {curl}, a select over a big table - is still being rendered.
  //
  // The response is otherwise one piece, sent after the whole page is rendered
  // (padWebSend, lib/output.php). A flushed response cannot be that piece, so the first
  // flush gives up for this request everything that needs the whole body: tidy (it
  // reflows the whole document), gzip of the whole body, the ETag and its 304,
  // Content-Length, and the page cache. The headers go out with the first chunk, so a
  // header or cookie the template sets after it is lost.
  //
  // Only the request's own page flushes, at its top level - in the wrapper or in the page,
  // not inside another tag: that is where everything above the tag is final. The level
  // engine resolves a level left to right, so when it reaches {flush} the text before it
  // is the rendered page up to there.
  //
  // padFlushCan   whether this request can flush at this point
  // padFlush      sends what is new since the last flush and remembers how much went
  // padFlushRest  at the end of the request, the part of the page not sent yet
  // padFlushSend  writes a chunk past PAD's output buffers, which keep whatever they hold
  //
  // A request answered with a part of the page - one response fragment (lib/respond.php),
  // the post of a live region (lib/live.php) - flushes nothing: what goes out is that part
  // alone, and the head of the page a flush above it sent went in front of it, into the
  // element an HTMX swap or a live event replaces.
  //
  // Nor does a page an application's _events/output.php sees (lib/events.php): the hook is
  // handed the page as it will go out, whole, and may change any of it - the part a flush
  // sent went out past it, so a hook that redacted a word redacted it below the flush only.

  function padFlushCan () {

    global $pad, $padOutputType, $padStop, $padCacheStop, $padFragmentOnly;

    return ( $pad == 1
             and $padOutputType == 'web'
             and PHP_SAPI != 'cli'
             and $padCacheStop != 200
             and (string) $padFragmentOnly === ''
             and padLive () === ''
             and ! padFlushDocument ()
             and ! padEventCheck ( 'output' ) );

  }

  // Whether the request answers a document instead of the page - a {pdf}: asked for it
  // with &padPdf, or a {pdf} without preview in the page. It flushes nothing: the head of
  // the page a flush sent went out in front of the PDF's bytes, the answer text/html.

  function padFlushDocument () {

    if ( isset ( $_GET ['padPdf'] ) )
      return TRUE;

    preg_match_all ( '/\{(?:pad:)?pdf(?=[\s}])[^}]*\}/', (string) ( $GLOBALS ['padBase'] [0] ?? '' ), $found );

    // Only the bare option is a preview - {pdf preview}, {pdf file='x', preview}: the word in a
    // title or a file name, or preview=$draft that may be false, took the document for a
    // preview and sent the head of the page in front of its bytes.

    foreach ( $found [0] as $tag )
      if ( ! preg_match ( '/(?:^\{\S+|,)\s*preview\s*(?=[,}|])/', $tag ) )
        return TRUE;

    return FALSE;

  }

  function padFlush () {

    global $padOut, $padStart, $padFlushRaw, $padTidy, $padMyTidy, $padGzip, $padCache,
           $padWebEtag304, $padLen, $padCsrf, $padCheckSyntax;

    $raw = substr ( $padOut [0], 0, $padStart [0] );

    if ( ! str_starts_with ( $raw, $padFlushRaw ?? '' ) )
      return;

    $chunk = substr ( $raw, strlen ( $padFlushRaw ?? '' ) );

    if ( ! isset ( $padFlushRaw ) ) {

      $padTidy       = FALSE;
      $padMyTidy     = FALSE;
      $padGzip       = FALSE;
      $padCache      = FALSE;
      $padWebEtag304 = FALSE;
      $padLen        = 0;

      $padFlushRaw = '';

      // With $padCsrf on, the session that holds the forms' token starts now, while its
      // cookie can still go out with the headers: a form below the flush asked for it after
      // they had gone, could start no session, and carried an empty token that every post
      // of it failed (lib/csrf.php). Only when the page holds a form, or the visitor has a
      // session already: on every flushed page it gave each visitor - each bot - a session
      // and a cookie, which also kept that visitor out of the page cache from then on.

      if ( $padCsrf and ( isset ( $_COOKIE [ session_name () ] ) or preg_match ( '/<form\b|\{form\b/i', $padOut [0] ) ) )
        padCsrfToken ();

      // A visitor who brought a session has it opened now, while it still can be: below the
      // flush the login could not be read - {auth}, {guest}, {can} and padUser took a
      // logged-in user for a guest, and the menu after </head> said "Log in".

      if ( isset ( $_COOKIE [ session_name () ] ) )
        padSessionStart ();

      padWebHeaders ( 200 );

    }

    $padFlushRaw .= $chunk;

    // A {stack} in the part that goes now gets the pushes made so far: what is pushed after
    // the flush cannot reach text the browser already has. Its posting forms get the CSRF
    // token as exits/exits.php gives it to the rest of the page - the part sent early went
    // out without it.

    $chunk = padNocacheStrip ( padUnprotect ( padUnescape ( padStackFill ( $chunk ) ) ) );

    if ( $padCsrf ) {

      $chunk = padCsrfForms ( $chunk, TRUE, TRUE );

      // A posting form the flush cuts gets no token (lib/csrf.php), so each post of it is
      // refused 403 - the strict check says why, where nothing did.

      if ( ( $GLOBALS ['padCsrfSplit'] ?? FALSE ) and $padCheckSyntax )
        padError ( 'a {flush} stands inside a posting form, which then gets no CSRF token - put the flush outside it, after </head>' );

    } else
      padCsrfBaseKeep ( $chunk );

    padFlushSend ( $chunk );

  }

  // What the request still has to send: the page less the part that went out. The flushed
  // part is a prefix of the finished page - nothing to the left of a resolved tag changes -
  // and when, against that, it is not, the whole page goes rather than a broken one.

  function padFlushRest ( $result ) {

    global $padFlushRaw;

    if ( ! isset ( $padFlushRaw ) or ! str_starts_with ( $result, $padFlushRaw ) )
      return $result;

    return substr ( $result, strlen ( $padFlushRaw ) );

  }

  function padFlushSend ( $chunk ) {

    $saved = [];

    while ( ob_get_level () )
      $saved [] = ob_get_clean ();

    echo $chunk;

    flush ();

    foreach ( array_reverse ( $saved ) as $content ) {
      ob_start ();
      echo $content;
    }

  }

?>
