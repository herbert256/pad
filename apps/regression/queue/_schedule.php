<?php

  // The schedule the scheduler's pages and pad schedule read.

  return [
    [ 'every' => 'minute',          'job' => 'stamp', 'data' => [ 'what' => 'every minute' ] ],
    [ 'every' => '15 minutes',      'job' => 'greet', 'data' => [ 'name' => 'quarter' ], 'name' => 'quarter' ],
    [ 'every' => 'day at 03:00',    'job' => 'greet', 'data' => [ 'name' => 'night' ],   'name' => 'night', 'queue' => 'later' ],
    [ 'cron'  => '0 8 * * mon-fri', 'job' => 'greet', 'data' => [ 'name' => 'morning' ], 'name' => 'morning', 'overlap' => FALSE ],
    [ 'every' => 'month',           'job' => 'broken' ],
  ];

?>
