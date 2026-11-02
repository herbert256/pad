<?php

include_once PAD . 'subs/calendars/holidays/_lib/holidays.php';
$from = padHolidaysDay ( 2026, 1, 1 ); $to = padHolidaysDay ( 2026, 1, 6 );
$a = padHolidaysMerge ( 'nl,de-by,nl', $from, $to );
$b = padHolidaysMerge ( [ 'DE-BY', 'nl' ], $from, $to );
$newyear = array_values ( array_filter ( $a, fn ( $row ) => $row ['date'] === '2026-01-01' ) );
$epiphany = array_values ( array_filter ( $a, fn ( $row ) => $row ['date'] === '2026-01-06' ) );
$answer = $a === $b && count ( $newyear ) === 2 && array_column ( $newyear, 'place' ) === [ 'de-by', 'nl' ]
  && $newyear [0] ['region'] === '' && $newyear [0] ['sourceRegion'] === 'by'
  && $epiphany [0] ['country'] === 'de' && $epiphany [0] ['region'] === 'by'
  && ! isset ( $a [0] ['_order'] ) ? 'merged rows preserve country and requested region' : 'FAIL';

?>
