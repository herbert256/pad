<?php

include_once PAD . 'subs/calendars/holidays/_lib/holidays.php';
$a = padHolidaysDay ( 2026, 1, 1 ); $b = padHolidaysDay ( 2026, 12, 31 );
$answer = is_string ( padHolidaysMerge ( 'nl,not-a-country', $a, $b ) )
  && is_string ( padHolidaysMerge ( 'nz', padHolidaysDay ( 2053, 1, 1 ), padHolidaysDay ( 2053, 12, 31 ) ) )
  && is_string ( padHolidaysMerge ( [ [ 'nl' ] ], $a, $b ) )
  && is_string ( padHolidaysMerge ( 'nl', $b, $a ) )
  && is_string ( padHolidaysMerge ( 'nl,de', $a, $b, 'invalid' ) )
  && is_string ( padHolidaysMerge ( array_fill ( 0, 17, 'nl' ), $a, $b ) )
  ? 'invalid merged-calendar inputs report a validation message' : 'FAIL';

?>
