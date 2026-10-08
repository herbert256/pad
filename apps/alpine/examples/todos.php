<?php

  // The list is the session's, started from _data/todos.json. Every change is a post - op=add
  // with a text, toggle or remove with an id, clear for the done ones - and the answer is
  // the list as it now stands ($padExpose): the component shows a change at once and then
  // takes the server's list as the truth.

  $todos = padSession ( 'alpineTodos', alpineData ( 'todos' ) );

  if ( padRequestIs ( 'POST' ) ) {

    $op = (string) padRequest ( 'op', '' );
    $id = (int) padRequest ( 'id', 0 );

    if ( $op == 'add' and trim ( (string) padRequest ( 'text', '' ) ) !== '' )
      $todos [] = [ 'id' => max ( array_column ( $todos, 'id' ) ?: [ 0 ] ) + 1, 'text' => mb_substr ( trim ( padRequest ( 'text' ) ), 0, 80 ), 'done' => FALSE ];

    foreach ( $todos as $at => $todo ) {
      if ( $op == 'toggle' and $todo ['id'] == $id ) $todos [$at] ['done'] = ! $todo ['done'];
      if ( $op == 'remove' and $todo ['id'] == $id ) unset ( $todos [$at] );
    }

    if ( $op == 'clear' )
      $todos = array_filter ( $todos, fn ( $todo ) => ! $todo ['done'] );

    $todos = array_values ( $todos );

    padSessionPut ( 'alpineTodos', $todos );

  }

  $padExpose = [ 'todos' ];

?>
