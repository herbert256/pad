<?php
  function padRecurrenceInteger ( $value, $min, $max, $name ) {
    if ( ! is_int ( $value ) and ( ! is_string ( $value ) or ! preg_match ( '/^-?\d+$/D', $value ) ) ) throw new InvalidArgumentException ( "Invalid recurrence $name" );
    if ( $value < $min or $value > $max ) throw new InvalidArgumentException ( "Invalid recurrence $name" );
    return (int) $value;
  }

  // A repeated wall time has two instants: select the first. A skipped wall time has none.
  function padRecurrenceWall ( $wall, $zone ) {
    $utc = DateTimeImmutable::createFromFormat ( '!Y-m-d H:i:s', $wall, new DateTimeZone ( 'UTC' ) );
    if ( ! $utc or $utc->format ( 'Y-m-d H:i:s' ) !== $wall ) return NULL;
    $stamp = $utc->getTimestamp (); $matches = [];
    $transitions = $zone->getTransitions ( $stamp - 172800, $stamp + 172800 );
    $offsets = $transitions === FALSE ? [ $zone->getOffset ( $utc ) ] : array_column ( $transitions, 'offset' );
    foreach ( array_unique ( $offsets ) as $offset ) {
      $candidate = ( new DateTimeImmutable ( '@' . ( $stamp - $offset ) ) )->setTimezone ( $zone );
      if ( $candidate->format ( 'Y-m-d H:i:s' ) === $wall ) $matches [$candidate->getTimestamp ()] = $candidate;
    }
    if ( ! $matches ) return NULL;
    ksort ( $matches, SORT_NUMERIC ); return reset ( $matches );
  }

  function padRecurrenceDate ( $value, $zone ) {
    if ( $value instanceof DateTimeInterface ) return DateTimeImmutable::createFromInterface ( $value )->setTimezone ( $zone );
    if ( ! is_string ( $value ) or ! preg_match ( '/^(\d{4}-\d{2}-\d{2})(?:[T ](\d{2}:\d{2})(?::(\d{2}))?(Z|[+-]\d{2}:\d{2})?)?$/D', $value, $m ) ) throw new InvalidArgumentException ( 'Recurrence dates must be explicit ISO dates or date-times' );
    $wall = $m [1] . ' ' . ( $m [2] ?? '00:00' ) . ':' . ( $m [3] ?? '00' );
    $offset = $m [4] ?? '';
    if ( $offset !== '' and $offset !== 'Z' and ( (int) substr ( $offset, 4, 2 ) > 59 or (int) substr ( $offset, 1, 2 ) * 60 + (int) substr ( $offset, 4, 2 ) > 840 ) ) throw new InvalidArgumentException ( 'Invalid UTC offset' );
    $date = padRecurrenceWall ( $wall, $offset === '' ? $zone : new DateTimeZone ( $offset === 'Z' ? 'UTC' : $offset ) );
    if ( ! $date ) throw new InvalidArgumentException ( 'Invalid or nonexistent recurrence date-time' );
    return $date->setTimezone ( $zone );
  }

  function padRecurrenceDays ( $values ) {
    $names = [ 'MO' => 1, 'TU' => 2, 'WE' => 3, 'TH' => 4, 'FR' => 5, 'SA' => 6, 'SU' => 7 ];
    $out = [];
    foreach ( (array) $values as $value ) $out [] = isset ( $names [$value] ) ? $names [$value] : padRecurrenceInteger ( $value, 1, 7, 'weekday' );
    if ( ! $out ) throw new InvalidArgumentException ( 'Empty recurrence weekdays' );
    return array_unique ( $out );
  }

  function padRecurrenceExpand ( $kind, $type, $rule, $from, $to ) {
    $allowed = array_merge ( [ 'start', 'timezone', 'interval', 'count', 'until', 'exclude', 'limit' ], $type ['fields'] );
    if ( array_diff ( array_keys ( $rule ), $allowed ) ) throw new InvalidArgumentException ( 'Unsupported recurrence option' );
    $tz = $rule ['timezone'] ?? 'UTC';
    if ( ! is_string ( $tz ) or ! in_array ( $tz, DateTimeZone::listIdentifiers ( DateTimeZone::ALL_WITH_BC ), TRUE ) ) throw new InvalidArgumentException ( 'Recurrence needs an IANA timezone' );
    $zone = new DateTimeZone ( $tz );
    $start = padRecurrenceDate ( $rule ['start'] ?? NULL, $zone );
    $from = padRecurrenceDate ( $from, $zone ); $to = padRecurrenceDate ( $to, $zone );
    if ( $from >= $to ) throw new InvalidArgumentException ( 'Recurrence bounds must increase' );
    $dateOnly = is_string ( $rule ['start'] ) && strlen ( $rule ['start'] ) === 10;
    $interval = padRecurrenceInteger ( $rule ['interval'] ?? 1, 1, 10000, 'interval' );
    $limit = padRecurrenceInteger ( $rule ['limit'] ?? 1000, 1, 10000, 'limit' );
    $count = isset ( $rule ['count'] ) ? padRecurrenceInteger ( $rule ['count'], 1, 1000000, 'count' ) : PHP_INT_MAX;
    if ( isset ( $rule ['count'], $rule ['until'] ) ) throw new InvalidArgumentException ( 'COUNT and UNTIL are mutually exclusive' );
    $until = isset ( $rule ['until'] ) ? padRecurrenceDate ( $rule ['until'], $zone ) : NULL;
    if ( $until and $until < $start ) throw new InvalidArgumentException ( 'UNTIL precedes DTSTART' );
    $weekdays = padRecurrenceDays ( $rule ['byday'] ?? [ (int) $start->format ( 'N' ) ] );
    $weekstart = padRecurrenceDays ( [ $rule ['weekstart'] ?? 'MO' ] ) [0];
    $monthdays = [];
    foreach ( (array) ( $rule ['monthday'] ?? [ (int) $start->format ( 'j' ) ] ) as $v ) {
      $v = padRecurrenceInteger ( $v, -31, 31, 'monthday' );
      if ( ! $v ) throw new InvalidArgumentException ( 'Month day cannot be zero' );
      $monthdays [] = $v;
    }
    $months = [];
    foreach ( (array) ( $rule ['month'] ?? [ (int) $start->format ( 'n' ) ] ) as $v ) $months [] = padRecurrenceInteger ( $v, 1, 12, 'month' );
    if ( ! $months or ! $monthdays ) throw new InvalidArgumentException ( 'Empty recurrence month list' );
    $excluded = [];
    if ( isset ( $rule ['exclude'] ) and ! is_array ( $rule ['exclude'] ) ) throw new InvalidArgumentException ( 'Exclusions must be a list' );
    if ( count ( $rule ['exclude'] ?? [] ) > 10000 ) throw new LengthException ( 'Too many exclusions' );
    foreach ( $rule ['exclude'] ?? [] as $v ) {
      $ex = padRecurrenceDate ( $v, $zone );
      $excluded [ is_string ( $v ) && strlen ( $v ) === 10 ? $ex->format ( 'Y-m-d' ) : (string) $ex->getTimestamp () ] = TRUE;
    }
    $utc = new DateTimeZone ( 'UTC' );
    $day = new DateTimeImmutable ( $start->format ( 'Y-m-d' ), $utc );
    $last = new DateTimeImmutable ( $to->format ( 'Y-m-d' ), $utc );
    if ( $until ) $last = min ( $last, new DateTimeImmutable ( $until->format ( 'Y-m-d' ), $utc ) );
    if ( $last < $day ) return [];
    $span = (int) $day->diff ( $last )->days;
    if ( $span > 200000 ) throw new LengthException ( 'Recurrence scan exceeds 200000 days from DTSTART' );
    $origin = $day; $weekOffset = ( (int) $day->format ( 'N' ) - $weekstart + 7 ) % 7;
    $out = []; $seen = 0;
    for ( $d = 0; $d <= $span; $d++, $day = $day->modify ( '+1 day' ) ) {
      $monthDelta = ( (int) $day->format ( 'Y' ) - (int) $origin->format ( 'Y' ) ) * 12 + (int) $day->format ( 'n' ) - (int) $origin->format ( 'n' );
      $monthDay = in_array ( (int) $day->format ( 'j' ), $monthdays, TRUE ) || in_array ( (int) $day->format ( 'j' ) - (int) $day->format ( 't' ) - 1, $monthdays, TRUE );
      $matches = match ( $kind ) {
        'daily' => $d % $interval === 0,
        'weekly' => intdiv ( $d + $weekOffset, 7 ) % $interval === 0 and in_array ( (int) $day->format ( 'N' ), $weekdays, TRUE ),
        'monthly' => $monthDelta % $interval === 0 and $monthDay,
        'yearly' => ( (int) $day->format ( 'Y' ) - (int) $origin->format ( 'Y' ) ) % $interval === 0 and in_array ( (int) $day->format ( 'n' ), $months, TRUE ) and $monthDay,
      };
      if ( $d === 0 and ! $matches ) throw new InvalidArgumentException ( 'DTSTART must match recurrence filters' );
      if ( ! $matches ) continue;
      $occurrence = padRecurrenceWall ( $day->format ( 'Y-m-d' ) . ' ' . $start->format ( 'H:i:s' ), $zone );
      if ( ! $occurrence ) continue;
      if ( $until and $occurrence > $until or $occurrence >= $to ) break;
      if ( ++$seen > $count ) break;
      if ( $occurrence < $from or isset ( $excluded [$occurrence->format ( 'Y-m-d' )] ) or isset ( $excluded [(string) $occurrence->getTimestamp ()] ) ) continue;
      if ( count ( $out ) >= $limit ) throw new LengthException ( 'Recurrence output exceeds limit; narrow the window' );
      $out [] = $occurrence->format ( $dateOnly ? 'Y-m-d' : 'Y-m-d\TH:i:sP' );
    }
    return $out;
  }

  function padRecurrenceRrule ( $rule ) {
    $text = $rule ['rrule'] ?? NULL; unset ( $rule ['rrule'] );
    if ( ! is_string ( $text ) or strlen ( $text ) > 2048 ) throw new InvalidArgumentException ( 'RRULE must be a bounded string' );
    $parts = [];
    foreach ( explode ( ';', preg_replace ( '/^RRULE:/', '', strtoupper ( $text ) ) ) as $part ) {
      if ( ! preg_match ( '/^([A-Z]+)=([^;]+)$/D', $part, $m ) or isset ( $parts [$m [1]] ) ) throw new InvalidArgumentException ( 'Invalid or duplicate RRULE part' );
      $parts [$m [1]] = $m [2];
    }
    if ( array_diff ( array_keys ( $parts ), [ 'FREQ', 'INTERVAL', 'COUNT', 'UNTIL', 'BYDAY', 'BYMONTHDAY', 'BYMONTH', 'WKST' ] ) ) throw new InvalidArgumentException ( 'Unsupported RRULE part' );
    $kind = strtolower ( $parts ['FREQ'] ?? '' );
    if ( ! in_array ( $kind, [ 'daily', 'weekly', 'monthly', 'yearly' ], TRUE ) ) throw new InvalidArgumentException ( 'Unsupported RRULE frequency' );
    foreach ( [ 'INTERVAL' => 'interval', 'COUNT' => 'count', 'BYDAY' => 'byday', 'BYMONTHDAY' => 'monthday', 'BYMONTH' => 'month', 'WKST' => 'weekstart' ] as $key => $name )
      if ( isset ( $parts [$key] ) ) {
        if ( array_key_exists ( $name, $rule ) ) throw new InvalidArgumentException ( 'Conflicting recurrence options' );
        $rule [$name] = str_starts_with ( $key, 'BY' ) ? explode ( ',', $parts [$key] ) : $parts [$key];
      }
    if ( isset ( $parts ['UNTIL'] ) ) {
      if ( isset ( $rule ['until'] ) ) throw new InvalidArgumentException ( 'Conflicting UNTIL' );
      $dateOnly = is_string ( $rule ['start'] ?? NULL ) && strlen ( $rule ['start'] ) === 10;
      if ( ! preg_match ( $dateOnly ? '/^(\d{4})(\d{2})(\d{2})$/D' : '/^(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})Z$/D', $parts ['UNTIL'], $m ) ) throw new InvalidArgumentException ( 'UNTIL must match DTSTART date type; timed UNTIL must be UTC' );
      $rule ['until'] = "$m[1]-$m[2]-$m[3]" . ( $dateOnly ? '' : "T$m[4]:$m[5]:$m[6]Z" );
    }
    return [ $kind, $rule ];
  }
?>
