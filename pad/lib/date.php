<?php

  // Dates for a page's PHP: "now" in the application's timezone, a value read as a date
  // whatever form it came in, and how long ago a moment was in words - what Laravel's now(),
  // today(), Carbon::parse() and diffForHumans() give, as plain functions.
  //
  // padNow        now as a DateTimeImmutable in the application's timezone, or with a
  //               format the formatted string
  // padNowFreeze  fixes "now" for the rest of the request - a test, a replay - and NULL
  //               lets the clock run again; padNow, padToday, padAgo, padDateParse's
  //               'tomorrow' and padLog's time stamp follow it
  // padToday      today at midnight, as padNow answers it
  // padDateParse  a DateTimeImmutable from a timestamp, a date in text or a
  //               DateTimeInterface; NULL for what is none of those
  // padAgo        a moment in words, from the moment now is or one given: just now,
  //               5 minutes ago, yesterday, in 3 weeks - English
  //
  // padDateZone    the application's timezone: $padTimezone when it names one, else PHP's
  // padDateFormat  a moment answered as it is, or formatted when a format is given
  // padDateStamp   a Unix timestamp, fraction and all, as a moment in that timezone
  // padDateAgoText the words for one unit and count, past or future
  //
  // The timezone is read when a function is called, not when the request starts: a page's
  // PHP that sets $padTimezone for itself is answered in that zone. A number is a Unix
  // timestamp, as the date pipe reads it; text is read the way PHP's DateTime reads it, with
  // "tomorrow" and "+1 week" counted from the frozen moment when the clock is frozen.

  function padDateZone () {

    static $zones = [];

    $name = $GLOBALS ['padTimezone'] ?? '';
    $name = ( $name === NULL or $name === FALSE ) ? '' : ( is_string ( $name ) ? trim ( $name ) : get_debug_type ( $name ) );

    if ( $name === '' )
      $name = date_default_timezone_get ();

    if ( isset ( $zones [$name] ) )
      return $zones [$name];

    try {
      return $zones [$name] = new DateTimeZone ( $name );
    } catch ( Throwable $e ) {
      padError ( "\$padTimezone names no timezone, '" . padMakeSafe ( $name, 40 ) . "'" );
      return $zones [$name] = new DateTimeZone ( date_default_timezone_get () );
    }

  }

  // The frozen moment is kept in $padNowFrozen, an engine name no request value can fill.
  // It is shown in the application's zone, whichever zone it was frozen in.

  function padNow ( $format = NULL ) {

    $frozen = $GLOBALS ['padNowFrozen'] ?? NULL;

    $now = ( $frozen instanceof DateTimeImmutable )
         ? $frozen->setTimezone ( padDateZone () )
         : new DateTimeImmutable ( 'now', padDateZone () );

    return padDateFormat ( 'padNow', $now, $format );

  }

  // A format is a string handed to DateTimeInterface::format; NULL answers the object.

  function padDateFormat ( $function, $date, $format ) {

    if ( $format === NULL )
      return $date;

    if ( ! is_string ( $format ) and ! $format instanceof Stringable ) {
      padError ( "$function: the format must be a string, not " . get_debug_type ( $format ) );
      return '';
    }

    return $date->format ( (string) $format );

  }

  // Freezing reads the moment the way padDateParse does, so a test can write
  // padNowFreeze ( '2026-01-15 10:00:00' ). It answers the frozen moment, or NULL when the
  // clock runs again; what is no date is named and leaves the clock as it was.

  function padNowFreeze ( $time = NULL ) {

    if ( $time === NULL ) {
      unset ( $GLOBALS ['padNowFrozen'] );
      return NULL;
    }

    $moment = padDateParse ( $time );

    if ( $moment === NULL ) {
      padError ( "padNowFreeze cannot read '" . padMakeSafe ( is_scalar ( $time ) ? (string) $time : get_debug_type ( $time ), 40 ) . "' as a date" );
      return NULL;
    }

    return $GLOBALS ['padNowFrozen'] = $moment;

  }

  function padToday ( $format = NULL ) {

    return padDateFormat ( 'padToday', padNow ()->setTime ( 0, 0, 0, 0 ), $format );

  }

  // NULL, '', a boolean, an array, a float that is no number, text that is no date - or a
  // date that does not exist, 2026-02-30, which PHP would quietly turn into 2 March - are
  // no date and answer NULL. A DateTimeInterface keeps its own zone, as does text that names
  // one; a timestamp and zone-less text are in the application's zone.

  function padDateParse ( $value ) {

    if ( $value instanceof DateTimeImmutable )
      return $value;

    if ( $value instanceof DateTimeInterface )
      return DateTimeImmutable::createFromInterface ( $value );

    if ( is_string ( $value ) or $value instanceof Stringable )
      $value = trim ( (string) $value );

    if ( is_int ( $value ) or is_float ( $value ) or ( is_string ( $value ) and is_numeric ( $value ) ) )
      return padDateStamp ( $value );

    if ( ! is_string ( $value ) or $value === '' )
      return NULL;

    try {

      // Text that says nothing of the date - 'tomorrow', '+1 week', '10:30' - is counted
      // from now, and a frozen now is the one it is counted from.

      $frozen = $GLOBALS ['padNowFrozen'] ?? NULL;
      $parts  = date_parse ( $value );

      if ( $parts ['error_count'] or $parts ['warning_count'] )
        return NULL;

      if ( $frozen instanceof DateTimeImmutable and $parts ['year'] === FALSE and $parts ['month'] === FALSE and $parts ['day'] === FALSE ) {
        $date = @$frozen->setTimezone ( padDateZone () )->modify ( $value );
        return ( $date instanceof DateTimeImmutable ) ? $date : NULL;
      }

      $date = new DateTimeImmutable ( $value, padDateZone () );

      $errors = DateTimeImmutable::getLastErrors ();

      if ( $errors and ( $errors ['warning_count'] or $errors ['error_count'] ) )
        return NULL;

      return $date;

    } catch ( Throwable $e ) {

      return NULL;

    }

  }

  // A timestamp with a fraction keeps its microseconds; one before 1970 too, the fraction
  // counted forward from the whole second below it.

  function padDateStamp ( $stamp ) {

    $stamp = (float) $stamp;

    if ( ! is_finite ( $stamp ) or abs ( $stamp ) > 253402300799 )
      return NULL;

    $seconds = (int) floor ( $stamp );
    $micro   = (int) round ( ( $stamp - $seconds ) * 1000000 );

    if ( $micro >= 1000000 ) {
      $seconds++;
      $micro = 0;
    }

    $date = DateTimeImmutable::createFromFormat ( 'U u', $seconds . ' ' . sprintf ( '%06d', $micro ) );

    return ( $date instanceof DateTimeImmutable ) ? $date->setTimezone ( padDateZone () ) : NULL;

  }

  // Under a minute is seconds - and under ten seconds just now - under an hour minutes,
  // under a day hours. From a day on the calendar counts: the day before today is
  // yesterday whatever the hour, then days, weeks, months and years between the two dates,
  // both seen in the timezone of now. NULL and '' answer ''; what is no date is named.

  function padAgo ( $date, $now = NULL ) {

    if ( $date === NULL or $date === '' )
      return '';

    $moment = padDateParse ( $date );

    if ( $moment === NULL ) {
      padError ( "padAgo cannot read '" . padMakeSafe ( is_scalar ( $date ) ? (string) $date : get_debug_type ( $date ), 40 ) . "' as a date" );
      return '';
    }

    if ( $now === NULL or $now === '' )
      $from = padNow ();
    else
      $from = padDateParse ( $now );

    if ( $from === NULL ) {
      padError ( "padAgo cannot read '" . padMakeSafe ( is_scalar ( $now ) ? (string) $now : get_debug_type ( $now ), 40 ) . "' as the moment to count from" );
      return '';
    }

    $moment  = $moment->setTimezone ( $from->getTimezone () );
    $seconds = $from->getTimestamp () - $moment->getTimestamp ();
    $future  = ( $seconds < 0 );
    $seconds = abs ( $seconds );

    if ( $seconds < 10    ) return 'just now';
    if ( $seconds < 60    ) return padDateAgoText ( $seconds, 'second', $future );
    if ( $seconds < 3600  ) return padDateAgoText ( intdiv ( $seconds, 60 ), 'minute', $future );
    if ( $seconds < 86400 ) return padDateAgoText ( intdiv ( $seconds, 3600 ), 'hour', $future );

    $start = $moment->setTime ( 0, 0, 0, 0 );
    $end   = $from->setTime ( 0, 0, 0, 0 );
    $span  = $future ? $end->diff ( $start ) : $start->diff ( $end );
    $days  = max ( 1, (int) $span->days );

    if ( $days == 1 ) return $future ? 'tomorrow' : 'yesterday';
    if ( $days < 7  ) return padDateAgoText ( $days, 'day', $future );

    if ( $span->y ) return padDateAgoText ( $span->y, 'year',  $future );
    if ( $span->m ) return padDateAgoText ( $span->m, 'month', $future );

    return padDateAgoText ( intdiv ( $days, 7 ), 'week', $future );

  }

  function padDateAgoText ( $count, $unit, $future ) {

    $words = $count . ' ' . $unit . ( $count == 1 ? '' : 's' );

    return $future ? "in $words" : "$words ago";

  }

?>
