<?php

  $title    = 'Todo List';
  $dataFile = 'demo/todos.json';   // under DATA/, for padFileGet and padFilePut
  $message  = $message ?? '';      // todoPost sets it, then restarts this page

  // A notice from the request - ?todo&message[]=x - is no text: {if $message} ended the
  // page on a 500 over the list.

  if ( ! is_string ( $message ) )
    $message = '';

  $todos = json_decode ( padFileGet ( $dataFile ), TRUE ) ?: [];

  $hasTodos     = count ( $todos ) > 0;
  $doneCount    = count ( padArrWhere ( $todos, 'done' ) );
  $pendingCount = count ( $todos ) - $doneCount;

?>
