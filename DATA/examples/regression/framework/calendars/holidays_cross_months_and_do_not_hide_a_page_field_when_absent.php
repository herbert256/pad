<?php

$holiday = 'page holiday';
  $week = calendarViewOutput ( '{calendar:week "2026-12-31", holidays="nl"}{pad:if $holiday}{$date}:{$holiday}{/pad:if}{/calendar:week}' );
  $day = calendarViewOutput ( '{calendar:day "2026-11-04"}{$holiday}{/calendar:day}' );
  $agenda = calendarViewOutput ( '{calendar:agenda "2026-04", holidays="nl"}{$date} {/calendar:agenda}' );
  $answer = str_contains ( $week, '2027-01-01:New Year' ) && trim ( $day ) === 'page holiday'
         && str_contains ( $agenda, '2026-04-27' ) ? 'next-year holiday, holiday agenda, page field preserved' : 'FAIL';

?>
