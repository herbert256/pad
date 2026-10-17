<?php

  // The board's event stream: every open window listens (EventSource) and asks for the
  // columns again when the board moved on. Every two seconds the version - the newest
  // activity - is looked at; when it is not the one the window last had, a 'board' event
  // goes out with the version as its id. On a server the stream runs for a minute and the
  // browser reconnects; in the browser (padWasm) each connection is one look, and the
  // browser comes back after two seconds - with the last id, so nothing is sent twice.

  // Asked for as a page - a link, a crawler - it says what it is instead.

  $kanbanPage = 'Events';

  if ( str_contains ( (string) ( $_SERVER ['HTTP_ACCEPT'] ?? '' ), 'text/event-stream' ) ) {

    $seen = (int) padSseLastId ();

    padSse ( function ( $send ) use ( &$seen ) {

      $version = kanbanVersion ();

      if ( $version != $seen ) {
        $seen = $version;
        $send ( 'board', kanbanSummary (), $version );
      }

      return NULL;

    }, [ 'every' => 2, 'for' => 60 ] );

  }

?>
