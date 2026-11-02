<?php

  $tasks = [
    [ 'id' => 't1', 'title' => 'Write the quarterly report' ],
    [ 'id' => 't2', 'title' => 'Call the bank about the loan' ],
    [ 'id' => 't3', 'title' => 'Order the new kettle boxes' ],
    [ 'id' => 't4', 'title' => 'Answer the mail of the week' ]
  ];

  // The form posts priority[key] = place for every task: sorted by place, the keys are the
  // order - only the keys this page wrote count.

  $saved = '';

  if ( padPosted ( 'ranking' ) ) {
    $titles = array_column ( $tasks, 'title', 'id' );
    $places = array_intersect_key ( (array) padRequest ( 'priority', [] ), $titles );
    asort ( $places );
    $saved  = implode ( ', ', array_map ( fn ( $key ) => $titles [$key], array_keys ( $places ) ) );
  }

?>
