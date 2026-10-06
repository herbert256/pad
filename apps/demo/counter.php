<?php

  // The counts live in DATA/demo/counter.json: padFileGet reads it - empty the first time -
  // and padFilePut writes it, making the directory when there is none. The day is the
  // application's own (padToday, in $padTimezone).

  $title = 'Page Counter';
  $today = padToday ( 'Y-m-d' );

  $data = json_decode ( padFileGet ( 'demo/counter.json' ), TRUE )
       ?: [ 'total' => 0, 'today' => 0, 'date' => $today ];

  if ( $data ['date'] != $today ) {
    $data ['today'] = 0;
    $data ['date']  = $today;
  }

  $data ['total']++;
  $data ['today']++;

  padFilePut ( 'demo/counter.json', json_encode ( $data, JSON_PRETTY_PRINT ) );

  $totalCount  = $data ['total'];
  $todayCount  = $data ['today'];
  $currentDate = padNow ( 'l, F j, Y' );

?>
