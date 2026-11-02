<?php

  // Easter Sunday, the day every Easter-relative holiday counts from, in the years around now,
  // the first Gregorian year, the earliest possible date (2285, 22 March) and the latest
  // (2038, 25 April) - read from the rows of padHolidays as a page reads them.

  $easter = [];

  foreach ( [ 2024, 2025, 2026, 2027, 2038, 1583, 2000, 2285 ] as $year )
    foreach ( padHolidays ( 'nl', $year ) as $row )
      if ( $row ['name'] == 'Easter Sunday' )
        $easter [] = $row ['date'];

  $easter = implode ( ' ', $easter );

?>
