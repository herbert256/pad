<?php

  // The demo todo's action page acts on a post only: a GET of ?todoPost&go=add - a link, an
  // <img> on any page - carries no CSRF token, and must not add a task. The marker is looked
  // for by its own text, and taken out again should it have got in, so request/todo, which
  // posts a marker of its own beside this one, is left alone.

  $todoStore = DATA . 'demo/todos.json';

  padCurl ( $padHost . 'demo/?todoPost&go=add&task=regression+get+marker' );

  $todoList  = json_decode ( padFileGet ( $todoStore, '[]' ), TRUE ) ?: [];
  $todoAdded = FALSE;

  foreach ( $todoList as $todoKey => $todoOne )
    if ( ( $todoOne ['task'] ?? '' ) == 'regression get marker' ) {
      $todoAdded = TRUE;
      unset ( $todoList [$todoKey] );
    }

  if ( $todoAdded )
    padFilePut ( $todoStore, json_encode ( array_values ( $todoList ), JSON_PRETTY_PRINT ) );

  $todoGetResult = 'added by a GET: ' . ( $todoAdded ? 'yes' : 'no' );

?>
