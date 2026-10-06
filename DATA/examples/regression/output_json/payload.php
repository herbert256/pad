<?php

  // A page of a json application: what it names in $padExpose is its answer, and $hidden,
  // which it does not name, never leaves the server.

  $greet  = 'carried all the way';
  $items  = [ [ 'name' => 'one', 'n' => 1 ], [ 'name' => 'two', 'n' => 2 ] ];
  $hidden = 'not exposed';

  $padExpose = [ 'greet', 'items' ];

?>
