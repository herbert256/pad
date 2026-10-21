<?php

  // The schedule the scheduler's pages and pad schedule read. The last two entries have a
  // fault each - a handler that is not there, an 'every' that cannot be read - and are left
  // out: the others are listed and run as ever, the two named as failed.

  return [
    [ 'every' => 'minute',          'job' => 'stamp', 'data' => [ 'what' => 'every minute' ] ],
    [ 'every' => '15 minutes',      'job' => 'greet', 'data' => [ 'name' => 'quarter' ], 'name' => 'quarter' ],
    [ 'every' => 'day at 03:00',    'job' => 'greet', 'data' => [ 'name' => 'night' ],   'name' => 'night', 'queue' => 'later' ],
    [ 'cron'  => '0 8 * * mon-fri', 'job' => 'greet', 'data' => [ 'name' => 'morning' ], 'name' => 'morning', 'overlap' => FALSE ],
    [ 'every' => 'month',           'job' => 'broken' ],
    [ 'cron'  => '0 8 12 10 *',     'job' => 'again', 'name' => 'again' ],
    [ 'every' => 'minute',          'job' => 'gone' ],
    [ 'every' => 'fortnight',       'job' => 'greet', 'name' => 'fortnight' ],
  ];

?>
