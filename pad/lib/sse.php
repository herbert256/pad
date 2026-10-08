<?php

  // Server-sent events: a page that does not answer once but keeps the connection open and
  // sends events while they happen - what EventSource reads in the browser, and what htmx's
  // sse extension, a React or an Alpine component listen to.
  //
  //   // ticker.php - the page's PHP; there is no template to render
  //   padSse ( function ( $send ) {
  //     foreach ( [ 'one', 'two', 'three' ] as $n => $word ) {
  //       $send ( 'word', [ 'n' => $n, 'word' => $word ] );
  //       sleep ( 1 );
  //     }
  //   } );
  //
  //   // or: PAD calls the producer every 2 seconds for at most a minute, and sends what it
  //   // answers as a 'stats' event - FALSE ends the stream, NULL sends nothing this time
  //   padSse ( fn () => padStats (), [ 'every' => 2, 'for' => 60, 'event' => 'stats' ] );
  //
  // padSse        streams the events the producer sends and ends the request
  // padSseSend    writes one event - name, data (an array goes as JSON), id - past PAD's
  //               output buffers, and answers whether the visitor is still connected
  // padSseLastId  the id of the last event the browser had, sent back on a reconnect -
  //               the Last-Event-ID header, or lastEventId in the query
  // padSseLine    one field of an event, with what would end the field taken out
  //
  // The session is written and closed before the first event: PHP locks a session file
  // for as long as a request holds it, and every other request of the same visitor - the
  // next page, a fetch of the same component - waited for the stream to end. A stream
  // holds a PHP worker for as long as it runs, so 'for' bounds a loop (60 seconds unless
  // said otherwise), and the browser's EventSource reconnects by itself, after 'retry'
  // milliseconds when the page says so.
  //
  // No gzip, no Content-Length, no ETag, no page cache: the answer is never one piece.
  // X-Accel-Buffering: no asks nginx not to hold the events back; under Apache's
  // mod_deflate a stream has to be left out of compression by the server's configuration.

  function padSse ( $producer, $options = [] ) {

    global $padContentType, $padGzip, $padLen, $padCache, $padWebEtag304;

    if ( ! is_callable ( $producer ) )
      return padError ( 'padSse: the producer must be a function - padSse ( function ( $send ) { ... } )' );

    if ( ! is_array ( $options ) )
      return padError ( 'padSse: the options must be an array - [ \'every\' => 2, \'for\' => 60 ]' );

    foreach ( array_keys ( $options ) as $key )
      if ( ! in_array ( $key, [ 'every', 'for', 'retry', 'event' ], TRUE ) )
        return padError ( "padSse: there is no option named '" . padMakeSafe ( (string) $key, 20 ) . "' - every, for, retry, event" );

    if ( headers_sent () )
      return padError ( 'padSse: the headers have gone out already - a stream is the whole answer of a page, from its PHP, before anything was sent' );

    $every = (float) ( $options ['every'] ?? 0 );
    $for   = (float) ( $options ['for']   ?? 60 );
    $event = (string) ( $options ['event'] ?? 'message' );

    if ( $every < 0 or $for <= 0 or ( isset ( $options ['retry'] ) and (int) $options ['retry'] < 0 ) )
      return padError ( 'padSse: every, for and retry are numbers above zero' );

    padCloseSession ();
    padEmptyBuffers ( $padSseIgnored );

    $padContentType = 'text/event-stream; charset=utf-8';
    $padGzip        = FALSE;
    $padLen         = 0;
    $padCache       = FALSE;
    $padWebEtag304  = FALSE;

    padWebHeaders ( 200 );
    padHeader ( 'X-Accel-Buffering: no' );

    $GLOBALS ['padSent'] = TRUE;

    if ( $every )
      set_time_limit ( (int) ceil ( $for ) + 30 );

    if ( isset ( $options ['retry'] ) )
      padFlushSend ( 'retry: ' . (int) $options ['retry'] . "\n\n" );

    $send = fn ( $name, $data = '', $id = NULL ) => padSseSend ( $name, $data, $id );

    if ( ! $every )
      $producer ( $send, padSseLastId () );

    else {

      // Each call is timed from the start, not from the end of the call before: the time a
      // producer and its send take would otherwise add up, and thirty calls a second apart
      // were twenty-nine within thirty seconds.

      $start = microtime ( TRUE );
      $tick  = 0;

      while ( TRUE ) {

        $answer = $producer ( $send, padSseLastId () );

        if ( $answer === FALSE )
          break;

        if ( $answer !== NULL and $answer !== TRUE and ! padSseSend ( $event, $answer ) )
          break;

        $next = $start + ( ++$tick ) * $every;

        if ( connection_aborted () or $next - $start > $for )
          break;

        $wait = $next - microtime ( TRUE );

        if ( $wait > 0 )
          usleep ( (int) ( $wait * 1000000 ) );

      }

    }

    padExit ( 200 );

  }

  // One event: event:, id: and a data: line for every line of the data. A field cannot
  // hold a line break - it ends the field - so the name and the id lose theirs, and text
  // data is split over data: lines, which the browser joins again with \n.

  function padSseSend ( $event, $data = '', $id = NULL ) {

    if ( ! is_string ( $data ) )
      $data = json_encode ( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR );

    $text = '';

    if ( (string) $event !== '' and $event !== 'message' )
      $text .= 'event: ' . padSseLine ( $event ) . "\n";

    if ( $id !== NULL )
      $text .= 'id: ' . padSseLine ( $id ) . "\n";

    foreach ( explode ( "\n", str_replace ( [ "\r\n", "\r" ], "\n", (string) $data ) ) as $line )
      $text .= "data: $line\n";

    padFlushSend ( "$text\n" );

    return ! connection_aborted ();

  }

  function padSseLine ( $value ) {

    return str_replace ( [ "\r", "\n" ], '', (string) $value );

  }

  function padSseLastId () {

    $id = $_SERVER ['HTTP_LAST_EVENT_ID'] ?? $_GET ['lastEventId'] ?? '';

    return is_string ( $id ) ? padSseLine ( $id ) : '';

  }

?>
