<?php

  $title = 'Clock';

  $currentTime     = date ( 'H:i:s' );
  $currentDate     = date ( 'l, F j, Y' );
  $timezone        = date_default_timezone_get ();
  $timestamp       = time ();
  $dayOfYear       = date ( 'z' ) + 1;
  $weekOfYear      = date ( 'W' );
  $daysInMonth     = date ( 't' );
  $isLeapYear      = date ( 'L' ) ? 'Yes' : 'No';
  $dayOfWeek       = date ( 'l' );
  $monthName       = date ( 'F' );
  $year            = date ( 'Y' );

  // Calculate days remaining in year
  $daysInYear      = date ( 'L' ) ? 366 : 365;
  $daysRemaining   = $daysInYear - $dayOfYear;

  // Different time formats
  $time12h         = date ( 'g:i:s A' );
  $time24h         = date ( 'H:i:s' );
  $iso8601         = date ( 'c' );
  $rfc2822         = date ( 'r' );

?>
