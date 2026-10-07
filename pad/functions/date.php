<?php

  // Pipe function date(format, modifier), also reached under the names time and timestamp:
  // formats the Unix timestamp piped in as the value. An empty or zero value means now.
  // With no argument the format is the $padFmtDate global ('Y-m-d' by default); a second
  // argument is a strtotime modifier applied to the value first, so date('Y-m-d', '+1 week')
  // shifts the timestamp before formatting it.
  //
  // A value that is not a number is a date written as text - '2026-10-02', 'next monday' -
  // and is read with strtotime: date() takes only a timestamp and threw on it. Text that is
  // no date at all answers empty, and strict mode says so.
  //
  // The timezone is the application's as the call finds it (padDateZone, lib/date.php), the
  // one localDate and ago use: a $padTimezone the page's PHP sets for itself is the zone the
  // text is read in, the modifier counted in and the date written in. PHP's date() and
  // strtotime() kept the zone the request started with, so in a page set to Tokyo the date
  // pipe wrote 22:13 UTC where localDate wrote 07:13 for the same moment.

  global $padFmtDate, $padCheckSyntax;

  $padDateZone = padDateZone ();

  if ( ! $value )
    $value = time ();
  elseif ( ! is_numeric ( $value ) ) {
    $padDateText = $value;
    try {
      $value = ( new DateTimeImmutable ( (string) $value, $padDateZone ) ) -> getTimestamp ();
    } catch ( Exception $padDateError ) {
      $value = FALSE;
    }
    if ( $value === FALSE ) {
      if ( $padCheckSyntax )
        padError ( "the date function cannot read '$padDateText' as a date" );
      return '';
    }
  }
  else
    $value = (int) $value;

  if ( $count == 0 ) {

    $format = $padFmtDate;

  } elseif ( $count == 1 ) {

    $format = $parm [0];

  } else {

    $format = $parm [0];

    // A modifier strtotime cannot read answered FALSE, which date() took for 0: 1970.
    //
    // A modifier that names a date and no time - '1 jan', '2024-02-03' - starts that day, as
    // strtotime has it; modify keeps the time of day, and date('Y-m-d H:i', '1 jan') wrote
    // 22:13 where it wrote 00:00.

    try {
      $padDateShift = ( new DateTimeImmutable ( "@$value" ) ) -> setTimezone ( $padDateZone )
                                                             -> modify ( (string) $parm [1] );
      $padDateParts = date_parse ( (string) $parm [1] );
      if ( $padDateParts ['hour'] === FALSE and ( $padDateParts ['year'] !== FALSE
           or $padDateParts ['month'] !== FALSE or $padDateParts ['day'] !== FALSE ) )
        $padDateShift = $padDateShift -> setTime ( 0, 0 );
      $padDateShift = $padDateShift -> getTimestamp ();
    } catch ( Exception $padDateError ) {
      $padDateShift = FALSE;
    }

    if ( $padDateShift === FALSE ) {
      if ( $padCheckSyntax )
        padError ( "the date function cannot read '" . padMakeSafe ( (string) $parm [1], 40 ) . "' as a change of date" );
      return '';
    }

    $value = $padDateShift;

  }

  return ( new DateTimeImmutable ( "@$value" ) ) -> setTimezone ( $padDateZone ) -> format ( (string) $format );

?>
