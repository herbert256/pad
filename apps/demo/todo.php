<?php

  $title = 'Todo List';
  $dataFile = DAT . 'demo/todos.json';
  $message = '';

  // Ensure data directory exists
  if ( ! is_dir ( DAT . 'demo' ) )
    @mkdir ( DAT . 'demo', 0755, TRUE );

  // Load existing todos
  $todos = [];
  if ( file_exists ( $dataFile ) ) {
    $json = file_get_contents ( $dataFile );
    $todos = json_decode ( $json, TRUE ) ?: [];
  }

  // Handle form submissions
  if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) switch ( $action ?? '' ) {

    case 'add':
      $task = trim ( $task ?? '' );
      if ( $task ) {
        $todos [] = [
          'id'   => uniqid (),
          'task' => htmlspecialchars ( $task ),
          'done' => FALSE,
          'date' => date ( 'Y-m-d H:i:s' )
        ];
        $message = 'Task added!';
      }
      break;

    case 'toggle':
      foreach ( $todos as &$todo )
        if ( $todo ['id'] == $id )
          $todo ['done'] = ! $todo ['done'];
      $message = 'Task updated!';
      break;

    case 'delete':
      $todos = array_filter ( $todos, fn($t) => $t ['id'] != $id );
      $todos = array_values ( $todos );
      $message = 'Task deleted!';
      break;

    case 'clear':
      $todos = array_filter ( $todos, fn($t) => ! $t ['done'] );
      $todos = array_values ( $todos );
      $message = 'Completed tasks cleared!';
      break;
  }

  if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $action )
    file_put_contents ( $dataFile, json_encode ( $todos, JSON_PRETTY_PRINT ) );

  $hasTodos = count ( $todos ) > 0;
  $doneCount = count ( array_filter ( $todos, fn($t) => $t ['done'] ) );
  $pendingCount = count ( $todos ) - $doneCount;

?>
