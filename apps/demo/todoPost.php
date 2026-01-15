<?php

  include APP . 'todo.php';

  switch ( $go ) {

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

    case 'done':
    
      foreach ( $todos as &$todo )
        if ( $todo ['id'] == $id )
          $todo ['done'] = 1;
      $message = 'Task marked as done!';
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

  file_put_contents ( $dataFile, json_encode ( $todos, JSON_PRETTY_PRINT ) );

  padRestart ( 'todo' );

?>