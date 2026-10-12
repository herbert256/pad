<?php

  // The application's own checks: the queue is fine, unless ?behind says it is not. With
  // ?claim it answers for the database too - a check of the engine's, which an application
  // can add a failure to but never answer away.

  return [
    'queue' => isset ( $_GET ['behind'] ) ? 'the queue is 42 jobs behind' : TRUE,
    'disk'  => TRUE
  ] + ( isset ( $_GET ['claim'] ) ? [ 'database' => TRUE ] : [] );

?>
