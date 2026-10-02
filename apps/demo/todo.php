<?php

  $title    = 'Todo List';
  $dataFile = DATA . 'demo/todos.json';
  $message  = $message ?? '';   // todoPost sets it, then restarts this page

  if ( ! is_dir ( DATA . 'demo' ) )
    @mkdir ( DATA . 'demo', 0755, TRUE );

  $todos = [];

  if ( file_exists ( $dataFile ) ) {
    $json  = file_get_contents ( $dataFile );
    $todos = json_decode ( $json, TRUE ) ?: [];
  }

  $hasTodos     = count ( $todos ) > 0;
  $doneCount    = count ( array_filter ( $todos, fn($t) => $t ['done'] ) );
  $pendingCount = count ( $todos ) - $doneCount;

?>