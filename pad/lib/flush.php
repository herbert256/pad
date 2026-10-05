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

  function padFlushCan () {

    global $pad, $padOutputType, $padStop, $padCacheStop;

    return ( $pad == 1
             and $padOutputType == 'web'
             and PHP_SAPI != 'cli'
             and $padCacheStop != 200 );

  }

  function padFlush () {

    global $padOut, $padStart, $padFlushRaw, $padTidy, $padMyTidy, $padGzip, $padCache,
           $padWebEtag304, $padLen, $padCsrf;

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

      padWebHeaders ( 200 );

    }

    $padFlushRaw .= $chunk;

    // A {stack} in the part that goes now gets the pushes made so far: what is pushed after
    // the flush cannot reach text the browser already has. Its posting forms get the CSRF
    // token as exits/exits.php gives it to the rest of the page - the part sent early went
    // out without it.

    $chunk = padUnprotect ( padUnescape ( padStackFill ( $chunk ) ) );

    if ( $padCsrf )
      $chunk = padCsrfForms ( $chunk );

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