<?php

  // iCalendar (RFC 5545), the reader and the writer of types/ics.php.
  //
  // The reader takes the VEVENTs of every VCALENDAR in the text, each a row with the same
  // fields, empty where the event has none:
  //
  //   uid, summary, start, end, allday, timezone, location, description, url, status,
  //   categories, rrule
  //
  // start and end are ISO texts: 2026-11-01 for a day (allday TRUE), 2026-11-01T09:30:00Z
  // for a moment in UTC, 2026-11-01T09:30:00 for a local one - floating, or in the zone of
  // its TZID, which timezone holds. The end of a day-long event is the day after its last, as
  // iCalendar has it; an event with a DURATION ends that long after its start; one with
  // neither lasts a day when it is a day and no time when it is a moment (RFC 5545 3.6.1).
  // A text value is unescaped - \n a newline, \, a comma; categories are joined by ', ';
  // rrule is the rule as written. The alarms and the time zones are read past.
  //
  // The writer writes a VCALENDAR of rows with those fields - a row with a title, as a feed's
  // rows have, has its title as the SUMMARY and its summary as the DESCRIPTION, and date
  // stands in for a missing start - every line folded at 75 octets
  // and ended with CRLF, every text escaped. Deterministic, the same rows giving the same
  // bytes: the UID is the row's uid or a UUID made of its start, end, summary and location;
  // the DTSTAMP the row's stamp, the stamp= option, or else the event's own start - never the
  // clock. A moment with a zone - Z, +02:00, a timezone field - is written in UTC, a local one
  // floating, a day as a DATE.

  include_once PAD . 'subs/formats/_lib/lines.php';

  function padIcsRead ( $text ) {

    $lines = padLinesRead ( $text );

    if ( ! $lines or $lines [0] [1] != 'BEGIN' or strtoupper ( $lines [0] [3] ) != 'VCALENDAR' )
      padFormatFail ( $lines [0] [0] ?? 1, 'an iCalendar text opens with BEGIN:VCALENDAR' );

    $stack = [];
    $rows  = [];
    $event = NULL;

    foreach ( $lines as [ $at, $name, $params, $value ] ) {

      if ( $name == 'BEGIN' ) {

        $what = strtoupper ( trim ( $value ) );

        if ( ! $stack and $what != 'VCALENDAR' )
          padFormatFail ( $at, "BEGIN:$what stands outside a VCALENDAR" );

        $stack [] = $what;

        if ( $what == 'VEVENT' and count ( $stack ) == 2 )
          $event = [ 'line' => $at, 'props' => [] ];

      } elseif ( $name == 'END' ) {

        $what = strtoupper ( trim ( $value ) );

        if ( ! $stack )
          padFormatFail ( $at, "END:$what closes nothing" );

        if ( end ( $stack ) != $what )
          padFormatFail ( $at, "END:$what closes BEGIN:" . end ( $stack ) );

        array_pop ( $stack );

        if ( $what == 'VEVENT' and $event !== NULL and count ( $stack ) == 1 ) {
          $rows [] = padIcsEvent ( $event );
          $event   = NULL;
        }

      } elseif ( ! $stack )
        padFormatFail ( $at, "$name stands outside a VCALENDAR" );

      elseif ( $event !== NULL and count ( $stack ) == 2 )
        $event ['props'] [$name] [] = [ $at, $params, $value ];

    }

    if ( $stack )
      padFormatFail ( end ( $lines ) [0], 'BEGIN:' . end ( $stack ) . ' is not closed by END:' . end ( $stack ) );

    return $rows;

  }

  function padIcsEvent ( $event ) {

    $props = $event ['props'];
    $one   = fn ( $name ) => $props [$name] [0] ?? NULL;
    $text  = fn ( $name ) => isset ( $props [$name] ) ? padLinesText ( $props [$name] [0] [2] ) : '';

    $start = $one ( 'DTSTART' ) ? padIcsDate ( $one ( 'DTSTART' ) ) : [ '', FALSE, '' ];

    if ( $one ( 'DTEND' ) )
      $end = padIcsDate ( $one ( 'DTEND' ) ) [0];
    elseif ( $one ( 'DURATION' ) )
      $end = padIcsAfter ( $start, $one ( 'DURATION' ) );
    elseif ( $start [1] )
      $end = padIcsAfter ( $start, [ 0, [], 'P1D' ] );
    else
      $end = $start [0];

    $categories = [];

    foreach ( $props ['CATEGORIES'] ?? [] as [ , , $value ] )
      foreach ( padLinesSplit ( $value, ',' ) as $category )
        $categories [] = padLinesText ( $category );

    return [
      'uid'         => $text ( 'UID' ),
      'summary'     => $text ( 'SUMMARY' ),
      'start'       => $start [0],
      'end'         => $end,
      'allday'      => $start [1],
      'timezone'    => $start [2],
      'location'    => $text ( 'LOCATION' ),
      'description' => $text ( 'DESCRIPTION' ),
      'url'         => trim ( $one ( 'URL' ) [2] ?? '' ),
      'status'      => strtoupper ( trim ( $one ( 'STATUS' ) [2] ?? '' ) ),
      'categories'  => implode ( ', ', $categories ),
      'rrule'       => trim ( $one ( 'RRULE' ) [2] ?? '' )
    ];

  }

  // A DTSTART or DTEND: [ the ISO text, whether it is a day, its TZID ].

  function padIcsDate ( $line ) {

    [ $at, $params, $value ] = $line;

    $value = trim ( $value );
    $zone  = $params ['TZID'] [0] ?? '';

    if ( preg_match ( '/^(\d{4})(\d\d)(\d\d)$/D', $value, $m ) and checkdate ( (int) $m [2], (int) $m [3], (int) $m [1] ) )
      return [ "{$m[1]}-{$m[2]}-{$m[3]}", TRUE, '' ];

    if ( strtoupper ( $params ['VALUE'] [0] ?? 'DATE-TIME' ) != 'DATE'
         and preg_match ( '/^(\d{4})(\d\d)(\d\d)T(\d\d)(\d\d)(\d\d)(Z?)$/Di', $value, $m )
         and checkdate ( (int) $m [2], (int) $m [3], (int) $m [1] ) and $m [4] < 24 and $m [5] < 60 and $m [6] <= 60 )
      return [ "{$m[1]}-{$m[2]}-{$m[3]}T{$m[4]}:{$m[5]}:{$m[6]}" . strtoupper ( $m [7] ), FALSE, $m [7] === '' ? $zone : '' ];

    padFormatFail ( $at, "'" . padMakeSafe ( $value, 30 ) . "' is no date of iCalendar - 20261101 for a day, 20261101T093000Z for a moment" );

  }

  // The end of an event that has a duration: its start and the DURATION (RFC 5545 3.3.6)
  // added, as wall-clock time - a day is a day, whatever the zone's clocks do.

  function padIcsAfter ( $start, $line ) {

    [ $at, , $value ] = $line;

    if ( $start [0] === '' )
      return '';

    if ( ! preg_match ( '/^([+-]?)P(?:(\d+)W|(?:(\d+)D)?(?:T(?=\d)(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?)?)$/D', trim ( $value ), $m ) or trim ( $value ) == 'P' or trim ( $value ) == 'PT' )
      padFormatFail ( $at, "'" . padMakeSafe ( $value, 30 ) . "' is no duration of iCalendar - P1D, PT1H30M, P2W" );

    $seconds = ( (int) ( $m [2] ?? 0 ) * 7 + (int) ( $m [3] ?? 0 ) ) * 86400
             + (int) ( $m [4] ?? 0 ) * 3600 + (int) ( $m [5] ?? 0 ) * 60 + (int) ( $m [6] ?? 0 );

    $days    = intdiv ( $seconds, 86400 );
    $rest    = $seconds % 86400;
    $sign    = ( $m [1] == '-' ) ? '-' : '+';
    $utc     = new DateTimeZone ( 'UTC' );
    $zulu    = str_ends_with ( $start [0], 'Z' );
    $moment  = new DateTimeImmutable ( rtrim ( $start [0], 'Z' ), $utc );
    $moment  = $moment->modify ( "$sign$days days" )->modify ( "$sign$rest seconds" );

    if ( $start [1] and $rest == 0 )
      return $moment->format ( 'Y-m-d' );

    return $moment->format ( 'Y-m-d\TH:i:s' ) . ( $zulu ? 'Z' : '' );

  }

  function padIcsWrite ( $rows, $options ) {

    $out = padLinesFold ( 'BEGIN:VCALENDAR' )
         . padLinesFold ( 'VERSION:2.0' )
         . padLinesFold ( 'PRODID:' . padLinesEscape ( $options ['prodid'] ?? '-//PAD//PAD formats//EN' ) )
         . padLinesFold ( 'CALSCALE:GREGORIAN' );

    if ( ( $options ['name'] ?? '' ) !== '' )
      $out .= padLinesFold ( 'X-WR-CALNAME:' . padLinesEscape ( $options ['name'] ) );

    foreach ( $rows as $index => $row ) {

      // A row with a title - a feed's, a page's - has its subject there and its summary is
      // the description; a row the reader made has its subject in summary.

      $titled  = padFormatPick ( $row, [ 'title' ] );
      $summary = $titled !== '' ? $titled : padFormatPick ( $row, [ 'summary', 'name' ] );
      $about   = padFormatPick ( $row, $titled !== '' ? [ 'description', 'summary', 'body' ] : [ 'description', 'body' ] );
      $zone    = padFormatPick ( $row, [ 'timezone', 'tzid' ] );
      $allday  = in_array ( strtolower ( padFormatCell ( $row ['allday'] ?? '' ) ), [ '1', 'true', 'yes' ], TRUE );
      $when    = padFormatPickValue ( $row, [ 'start', 'date', 'begin' ] );
      $start   = padIcsMoment ( $when, $zone, $allday );

      if ( $start === NULL ) {
        padFormatSkip ( 'row ' . ( $index + 1 ) . ' has no start an event can have - 2026-11-01 or 2026-11-01T09:30:00Z' );
        continue;
      }

      $end   = padIcsMoment ( padFormatPickValue ( $row, [ 'end', 'until' ] ), $zone, $start [2] );
      $uid   = padFormatPick ( $row, [ 'uid', 'id' ] )
               ?: padFormatUuid ( implode ( '|', [ $start [0], $end [0] ?? '', $summary, padFormatPick ( $row, [ 'location' ] ) ] ) );
      $stamp = padIcsStamp ( padFormatPickValue ( $row, [ 'stamp', 'updated' ] ) ?? ( $options ['stamp'] ?? NULL ), '' )
               ?? padIcsStamp ( $when, $zone );

      $out .= padLinesFold ( 'BEGIN:VEVENT' )
            . padLinesFold ( 'UID:' . padLinesEscape ( $uid ) )
            . padLinesFold ( 'DTSTAMP:' . $stamp )
            . padLinesFold ( 'DTSTART' . $start [1] . ':' . $start [0] );

      if ( $end !== NULL )
        $out .= padLinesFold ( 'DTEND' . $end [1] . ':' . $end [0] );

      foreach ( [ 'SUMMARY' => $summary, 'LOCATION' => padFormatPick ( $row, [ 'location' ] ), 'DESCRIPTION' => $about ] as $name => $value )
        if ( $value !== '' )
          $out .= padLinesFold ( "$name:" . padLinesEscape ( $value ) );

      if ( ( $url = padFormatPick ( $row, [ 'url', 'link' ] ) ) !== '' )
        $out .= padLinesFold ( 'URL:' . preg_replace ( '/[\r\n]/', '', $url ) );

      if ( ( $status = padFormatPick ( $row, [ 'status' ] ) ) !== '' )
        $out .= padLinesFold ( 'STATUS:' . strtoupper ( padLinesEscape ( $status ) ) );

      if ( ( $categories = padFormatPick ( $row, [ 'categories', 'tags' ] ) ) !== '' or is_array ( $row ['tags'] ?? NULL ) ) {
        $list = is_array ( $row ['categories'] ?? $row ['tags'] ?? NULL ) ? ( $row ['categories'] ?? $row ['tags'] ) : explode ( ',', $categories );
        $out .= padLinesFold ( 'CATEGORIES:' . implode ( ',', array_map ( fn ( $one ) => padLinesEscape ( trim ( (string) $one ) ), $list ) ) );
      }

      if ( ( $rrule = padFormatPick ( $row, [ 'rrule' ] ) ) !== '' )
        $out .= padLinesFold ( 'RRULE:' . preg_replace ( '/[\r\n]/', '', $rrule ) );

      $out .= padLinesFold ( 'END:VEVENT' );

    }

    return $out . padLinesFold ( 'END:VCALENDAR' );

  }

  // A moment of a row as a DTSTART, DTEND or DTSTAMP writes it: [ the value, its parameters,
  // whether it is a day ] - or NULL for none. A day: ;VALUE=DATE and 20261101; a moment with a
  // zone: UTC, 20261101T083000Z; a local one: floating, 20261101T093000.

  function padIcsMoment ( $value, $zone, $day ) {

    $date = padFormatDate ( $value, $zone );

    if ( $date === NULL )
      return NULL;

    [ $moment, $time, $written ] = $date;

    if ( ! $time or $day )
      return [ $moment->format ( 'Ymd' ), ';VALUE=DATE', TRUE ];

    if ( $written === '' )
      return [ $moment->format ( 'Ymd\THis' ), '', FALSE ];

    return [ $moment->setTimezone ( new DateTimeZone ( 'UTC' ) )->format ( 'Ymd\THis\Z' ), '', FALSE ];

  }

  // A moment as a DTSTAMP, which is always UTC: a day at its midnight, a local time as if in
  // UTC - or NULL for none.

  function padIcsStamp ( $value, $zone ) {

    $date = padFormatDate ( $value, $zone );

    return $date ? $date [0]->setTimezone ( new DateTimeZone ( 'UTC' ) )->format ( 'Ymd\THis\Z' ) : NULL;

  }

?>
