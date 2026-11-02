<?php

$ok = TRUE;
  foreach ( [ 'day' => [ '0000-01-01', '9999-12-31' ], 'week' => [ '0000-01-01', '9999-12-31' ],
              'year' => [ '0000', '9999' ], 'quarter' => [ '0000-Q1', '9999-Q4' ], 'agenda' => [ '0000-01', '9999-12' ] ] as $view => [ $first, $last ] ) {
    $query = $view == 'agenda' ? 'month' : $view;
    $_GET [$query] = $first;
    $start = calendarViewOutput ( "{calendar:$view/}" );
    $_GET [$query] = $last;
    $end = calendarViewOutput ( "{calendar:$view/}" );
    $ok = $ok && ! str_contains ( $start, 'rel="prev"' ) && ! str_contains ( $end, 'rel="next"' );
    unset ( $_GET [$query] );
  }
  $days = trim ( calendarViewOutput ( '{calendar:week "0000-01-01"}{$date} {/calendar:week}' ) );
  $answer = $ok && $days === '0000-01-01 0000-01-02' ? 'bounded navigation and clipped boundary week' : 'FAIL';

?>
