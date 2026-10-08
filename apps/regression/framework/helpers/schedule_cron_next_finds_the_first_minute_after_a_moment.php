<?php

  // padCronNext from fixed moments, in UTC and in Amsterdam across the end of summer time
  // (25 October 2026, 03:00 becomes 02:00).

  $padTimezone = 'UTC';

  $at   = fn ( $text ) => ( new DateTimeImmutable ( $text, padDateZone () ) )->getTimestamp ();
  $show = fn ( $time ) => $time === NULL ? NULL : ( new DateTimeImmutable ( "@$time" ) )->setTimezone ( padDateZone () )->format ( 'D Y-m-d H:i T' );

  $from = $at ( '2026-10-12 08:15' );

  $r = [
    'everyMinute'  => $show ( padCronNext ( '* * * * *',       $from ) ),
    'notItself'    => $show ( padCronNext ( '15 8 * * *',      $from ) ),
    'quarter'      => $show ( padCronNext ( '*/15 * * * *',    $from + 30 ) ),
    'weekday'      => $show ( padCronNext ( '0 8 * * mon-fri', $at ( '2026-10-16 09:00' ) ) ),
    'monthly'      => $show ( padCronNext ( '@monthly',        $from ) ),
    'leapDay'      => $show ( padCronNext ( '0 12 29 feb *',   $from ) ),
    'never'        => $show ( padCronNext ( '0 0 30 feb *',    $from ) ),
    'yearEnd'      => $show ( padCronNext ( '59 23 31 dec *',  $from ) ),
  ];

  $padTimezone = 'Europe/Amsterdam';

  $r ['amsterdam'] = $show ( padCronNext ( '30 2 * * *', $at ( '2026-10-24 12:00' ) ) );
  $r ['autumn']    = $show ( padCronNext ( '30 2 * * *', $at ( '2026-10-25 12:00' ) ) );

  $r = json_encode ( $r );

?>
