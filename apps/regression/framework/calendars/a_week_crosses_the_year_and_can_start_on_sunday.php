<?php

$monday = calendarViewOutput ( '{calendar:week "2026-12-31"}{$date} {/calendar:week}' );
  $sunday = calendarViewOutput ( '{calendar:week "2026-12-31", sunday}{$date} {/calendar:week}' );
  $answer = trim ( $monday ) === '2026-12-28 2026-12-29 2026-12-30 2026-12-31 2027-01-01 2027-01-02 2027-01-03'
         && trim ( $sunday ) === '2026-12-27 2026-12-28 2026-12-29 2026-12-30 2026-12-31 2027-01-01 2027-01-02'
           ? 'seven dates across the year in either week order' : 'FAIL';

?>
