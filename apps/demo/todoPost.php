<?php

  include APP . 'todo.php';

  $id = padRequest ( 'id', '' );

  switch ( padRequest ( 'go', '' ) ) {

    case 'add':

      $task = padRequest ( 'task', '' );
      if ( padFilled ( $task ) ) {
        $todos [] = [
          'id'   => uniqid (),
          'task' => htmlspecialchars ( $task ),
          'done' => FALSE,
          'date' => padNow ( 'Y-m-d H:i:s' )
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
    
      $todos = array_values ( padArrWhere ( $todos, 'id', '!=', $id ) );
      $message = 'Task deleted!';
      break;

    case 'clear':
    
      $todos = array_filter ( $todos, fn($t) => ! $t ['done'] );
      $todos = array_values ( $todos );
      $message = 'Completed tasks cleared!';
      break;
  
  }

  padFilePut ( $dataFile, json_encode ( $todos, JSON_PRETTY_PRINT ) );

  padRestart ( 'todo' );

?>
