<?php

  // A mail queued from a page in a directory of its own, its template in that directory's
  // _mail/: the worker renders it from there, as the page sending it at once would - a
  // worker's own page is the application's root, as pad work's is, so the page works the
  // queue from there. A template name that is nowhere is refused when the mail is queued.

  padQueueFlush ( 'mail' );

  $id = padMail ( 'ann@example.com', 'order', 'Order queued', [ 'n' => 2 ], [ 'queue' => 'mail' ] );

  $mailDir            = $GLOBALS ['padDir'];
  $GLOBALS ['padDir'] = '';

  $work = padQueueWork ( 'mail' );

  $GLOBALS ['padDir'] = $mailDir;

  $status = $work ['jobs'] [0] ['status'] ?? 'not run';
  $error  = $work ['jobs'] [0] ['error']  ?? '';
  $html   = ( $status == 'done' ) ? ( $GLOBALS ['padMailLast'] ['html'] ?? '' ) : '';

  @unlink ( padQueueAppDir () . "_failed/$id.json" );

  padQueueFlush ( 'mail' );


?>
