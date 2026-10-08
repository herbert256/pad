<?php

  // The application's own checks: the queue is fine, unless ?behind says it is not.

  return [
    'queue' => isset ( $_GET ['behind'] ) ? 'the queue is 42 jobs behind' : TRUE,
    'disk'  => TRUE
  ];

?>
