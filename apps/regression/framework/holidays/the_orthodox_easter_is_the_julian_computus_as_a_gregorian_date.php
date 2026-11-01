<?php

  // The Orthodox Easter Sunday, from which Romania counts Good Friday, Easter and Whitsun -
  // the years around now, one where it is the Gregorian one too (2025), and 2100 and 2101,
  // the Julian calendar thirteen days behind until March 2100 and fourteen after - read from
  // the rows of padHolidays as a page reads them.

  $easter = [];

  foreach ( [ 2023, 2024, 2025, 2026, 2027, 2100, 2101 ] as $year )
    foreach ( padHolidays ( 'ro', $year ) as $row )
      if ( $row ['name'] == 'Easter Sunday' )
        $easter [] = $row ['date'];

  $easter = implode ( ' ', $easter );

?>
