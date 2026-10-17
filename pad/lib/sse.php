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
  //
  // In the browser (padWasm, an application packed by pad pack) one PHP answers every
  // request of the tab, and a stream that held it would hold the whole application: there a
  // stream is one round, and EventSource - which reconnects by itself, with the last id -
  // makes the interval. The every form calls its producer once and asks the browser back
  // after 'every' seconds ('retry' when given); a producer answering FALSE with nothing sent
  // is answered 204, which tells EventSource to stop. The loop form gets 'retry' (a second
  // unless given) and ends at the first event sent a tenth of a second after the stream
  // began, once that event is written: the burst a producer sends at once goes out whole.

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

    $send = fn ( $name, $data = '', $id = NULL ) => padSseSend ( $name, $data, $id );

    if ( padWasm () and $every )
      padSseRound ( $producer, $send, $event, (int) ( $options ['retry'] ?? round ( $every * 1000 ) ) );

    padWebHeaders ( 200 );
    padHeader ( 'X-Accel-Buffering: no' );

    $GLOBALS ['padSent'] = TRUE;

    // A stream runs as long as 'for' says, and a while after for its last event - in both
    // forms: the loop form kept PHP's own limit, which some systems time on the wall clock,
    // sleeps included, and a producer sending thirty events a second apart was killed before
    // its last one and its done event - EventSource reconnected and it began again.

    set_time_limit ( (int) ceil ( $for ) + 30 );

    if ( padWasm () ) {
      $options ['retry']          = $options ['retry'] ?? 1000;
      $GLOBALS ['padSseBrowser'] = microtime ( TRUE );
    }

    if ( isset ( $options ['retry'] ) )
      padFlushSend ( 'retry: ' . (int) $options ['retry'] . "\n\n" );

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

  // The every form in the browser: the producer's one call comes before the headers, its
  // own sends held back, so that a FALSE with nothing sent can still be answered 204.

  function padSseRound ( $producer, $send, $event, $retry ) {

    global $padSseHold;

    $padSseHold = '';
    $answer     = $producer ( $send, padSseLastId () );
    $held       = $padSseHold;
    $padSseHold = NULL;

    if ( $answer === FALSE and $held === '' )
      padExit ( 204 );

    padWebHeaders ( 200 );
    padHeader ( 'X-Accel-Buffering: no' );

    $GLOBALS ['padSent'] = TRUE;

    padFlushSend ( "retry: $retry\n\n$held" );

    if ( $answer !== NULL and $answer !== TRUE and $answer !== FALSE )
      padSseSend ( $event, $answer );

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

    if ( is_string ( $GLOBALS ['padSseHold'] ?? NULL ) ) {
      $GLOBALS ['padSseHold'] .= "$text\n";
      return TRUE;
    }

    padFlushSend ( "$text\n" );

    if ( isset ( $GLOBALS ['padSseBrowser'] ) and microtime ( TRUE ) - $GLOBALS ['padSseBrowser'] > 0.1 )
      padExit ( 200 );

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
