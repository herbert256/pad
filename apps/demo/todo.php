<?php

  $title    = 'Todo List';
  $dataFile = 'demo/todos.json';   // under DATA/, for padFileGet and padFilePut
  $message  = $message ?? '';      // todoPost sets it, then restarts this page

  $todos = json_decode ( padFileGet ( $dataFile ), TRUE ) ?: [];

  $hasTodos     = count ( $todos ) > 0;
  $doneCount    = count ( padArrWhere ( $todos, 'done' ) );
  $pendingCount = count ( $todos ) - $doneCount;

?>
