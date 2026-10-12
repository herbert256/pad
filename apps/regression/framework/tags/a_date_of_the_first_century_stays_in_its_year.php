<?php

  // A date of the years 0 to 100 stays in its year - 0079-08-24, Vesuvius - where reading a
  // date as the wall clock of the application's timezone went through gmmktime, whose rule
  // for a two-digit year put it in 1979.

  $stamp = padChartStamp ( '0079-08-24' );
  $read  = $stamp === FALSE ? 'no date' : gmdate ( 'Y-m-d', $stamp );

?>
