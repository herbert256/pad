<?php

  // Queues jobs and works them in the same request: the queues are emptied first, so every
  // fetch of the page starts from nothing. flaky needs three attempts and gets them - its
  // backoff is 0, so its retries are due at once; broken, strict and warn fail every time
  // and end with the failed jobs; the delayed greet waits on its queue.

  padQueueFlush ( 'default' );
  padQueueFlush ( 'later' );
  padQueueFlush ( 'failed' );

  $queueHeard = [];

  padQueue ( 'greet',  [ 'name' => 'Alice' ] );
  padQueue ( 'flaky',  [], backoff: 0 );
  padQueue ( 'broken', [], tries: 2, backoff: 0 );
  padQueue ( 'strict', [ 'order' => 42 ], tries: 1 );
  padQueue ( 'warn',   [], tries: 1 );
  padQueue ( 'greet',  [ 'name' => 'Bob' ] );
  padQueue ( 'greet',  [ 'name' => 'Later' ], delay: 3600 );
  padQueue ( 'greet',  [ 'name' => 'Carol' ], queue: 'later' );

  $before = padQueueSize ();
  $work   = padQueueWork ();
  $after  = padQueueSize ();
  $queues = padQueueQueues ();

  $counts = "done {$work ['done']}, retried {$work ['retried']}, failed {$work ['failed']}";
  $runs   = array_map ( fn ( $job ) => [ 'job' => $job ['job'], 'status' => $job ['status'], 'attempts' => $job ['attempts'] ], $work ['jobs'] );
  // A retry that is due at once can come before or after the jobs queued with it, as the
  // clock ticks - the page shows them in an order of its own.

  usort ( $runs, fn ( $a, $b ) => [ $a ['job'], $a ['attempts'] ] <=> [ $b ['job'], $b ['attempts'] ] );
  sort  ( $queueHeard );

  $failures = array_map ( fn ( $job ) => [ 'job' => $job ['job'], 'attempts' => $job ['attempts'], 'error' => $job ['error'] ], padQueueFailed () );

  // Back on the queue, with their tries afresh: two of them run once more and fail again.

  $retried = padQueueRetry ( 'all' );
  $again   = padQueueWork ( 'default', 2 );
  $againRan = count ( $again ['jobs'] );
  $left    = padQueueSize ();

  padQueueFlush ( 'default' );
  padQueueFlush ( 'later' );
  padQueueFlush ( 'failed' );

?>
