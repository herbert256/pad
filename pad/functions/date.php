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

  global $padFmtDate, $padCheckSyntax;

  if ( ! $value )
    $value = time ();
  elseif ( ! is_numeric ( $value ) ) {
    $padDateText = $value;
    $value       = strtotime ( $value );
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
    $value  = strtotime ( $parm [1], $value );

  }

  return date ($format, $value);

?>