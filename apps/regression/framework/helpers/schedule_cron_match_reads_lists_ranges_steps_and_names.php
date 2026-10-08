<?php

  // padCronMatch at fixed moments, read in UTC: Monday 12 October 2026 08:15 and a few
  // around it.

  $padTimezone = 'UTC';

  $at = fn ( $text ) => ( new DateTimeImmutable ( $text, new DateTimeZone ( 'UTC' ) ) )->getTimestamp ();

  $monday = $at ( '2026-10-12 08:15' );

  $r = json_encode ( [
    'star'         => padCronMatch ( '* * * * *',         $monday ),
    'minute'       => padCronMatch ( '15 8 * * *',        $monday ),
    'otherMinute'  => padCronMatch ( '16 8 * * *',        $monday ),
    'step'         => padCronMatch ( '*/15 * * * *',      $monday ),
    'stepMiss'     => padCronMatch ( '*/10 * * * *',      $monday ),
    'rangeStep'    => padCronMatch ( '5-20/5 8 * * *',    $monday ),
    'list'         => padCronMatch ( '0,15,30,45 * * * *', $monday ),
    'hourRange'    => padCronMatch ( '15 9-17 * * *',     $monday ),
    'dayName'      => padCronMatch ( '15 8 * * mon',      $monday ),
    'dayRange'     => padCronMatch ( '15 8 * * mon-fri',  $monday ),
    'weekend'      => padCronMatch ( '15 8 * * sat,sun',  $monday ),
    'monthName'    => padCronMatch ( '15 8 * oct *',      $monday ),
    'monthMiss'    => padCronMatch ( '15 8 * nov *',      $monday ),
    'sundaySeven'  => padCronMatch ( '0 0 * * 7',         $at ( '2026-10-11 00:00' ) ),
    'sundayZero'   => padCronMatch ( '0 0 * * 0',         $at ( '2026-10-11 00:00' ) ),
    'domOrDow'     => padCronMatch ( '15 8 1 * mon',      $monday ),
    'domOrDowDom'  => padCronMatch ( '0 0 1 * mon',       $at ( '2026-10-01 00:00' ) ),
    'domStepDow'   => padCronMatch ( '15 8 */2 * mon',    $monday ),
    'daily'        => padCronMatch ( '@daily',            $at ( '2026-10-12 00:00' ) ),
    'hourly'       => padCronMatch ( '@hourly',           $monday ),
    'seconds'      => padCronMatch ( '15 8 * * *',        $monday + 59 ),
    'moment'       => padCronMatch ( '15 8 * * *',        new DateTimeImmutable ( '2026-10-12 10:15', new DateTimeZone ( 'Europe/Amsterdam' ) ) )
  ] );

?>
