<?php

  // A wall calendar no year= or month= fixes shows this year - a year the template does not
  // choose, so outside the years its country's file knows it is no error, as a month the
  // visitor asks {pad:calendar} for is none: only the days inside those years are marked. New
  // Zealand's holidays are known for 2010 to 2052 (Matariki's schedule): with the clock at
  // 2053 no day of the year is marked - the rules' rows marked King's Birthday and Labour Day
  // and left Matariki out - and at 2052 they are all there. A year= or month= outside them is
  // the strict check's error (the errors suite).

  $names = fn ( $show ) => implode ( ', ', array_unique ( preg_match_all ( "/Waitangi Day|Anzac Day|King's Birthday|Matariki|Labour Day|Christmas Day/", $show, $m ) ? $m [0] : [] ) ) ?: 'none';

  padNowFreeze ( '2053-03-01 12:00:00' );

  $after = $names ( calendarShow ( "{printable:calendar holidays='nz'/}" ) );

  padNowFreeze ( '2052-03-01 12:00:00' );

  $last = $names ( calendarShow ( "{printable:calendar holidays='nz'/}" ) );

  padNowFreeze ();

  $answer = "2053: $after // 2052: $last";

?>
