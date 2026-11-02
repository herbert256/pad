<?php

$body = '{array:weeks}{array:days}{pad:if ! $other}{$date} {/pad:if}{/array:days}{/array:weeks}';
  $year = explode ( ' ', trim ( calendarViewOutput ( "{calendar:year '2024'}$body{/calendar:year}" ) ) );
  $quarter = explode ( ' ', trim ( calendarViewOutput ( "{calendar:quarter '2024-Q1'}$body{/calendar:quarter}" ) ) );
  $answer = count ( $year ) === 366 && count ( array_unique ( $year ) ) === 366
         && count ( $quarter ) === 91 && count ( array_unique ( $quarter ) ) === 91
         && in_array ( '2024-02-29', $year, TRUE ) && end ( $quarter ) === '2024-03-31'
         && trim ( calendarViewOutput ( '{calendar:quarter "2024-05"}{$month} {/calendar:quarter}' ) ) === '2024-04 2024-05 2024-06'
           ? '366 year days and 91 quarter days, leap day once' : 'FAIL';

?>
