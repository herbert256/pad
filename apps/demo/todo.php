<?php

  $title    = 'Todo List';
  $dataFile = DAT . 'demo/todos.json';
  $message  = '';

  $todos = [];

  if ( file_exists ( $dataFile ) ) {
    $json  = file_get_contents ( $dataFile );
    $todos = json_decode ( $json, TRUE ) ?: [];
  }

  $hasTodos     = count ( $todos ) > 0;
  $doneCount    = count ( array_filter ( $todos, fn($t) => $t ['done'] ) );
  $pendingCount = count ( $todos ) - $doneCount;

?>