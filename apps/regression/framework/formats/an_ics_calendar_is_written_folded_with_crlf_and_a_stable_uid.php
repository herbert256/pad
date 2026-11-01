<?php

  // A calendar written from rows: CRLF line ends (shown as ~), a line longer than 75 octets
  // folded - never inside the ä - the continuation starting with a space; a text's ; , and
  // newline escaped; a moment with a zone in UTC, a local one floating, a day a DATE with the
  // next day as its end; the UID made of the row and the DTSTAMP its start, never the clock,
  // so writing it twice gives the same bytes; title and date read where summary and start
  // are missing. Read again, the rows come back.

  $icsRows = [
    [ 'summary' => 'Talk; "Formats", data in and out', 'start' => '2026-11-06T10:00:00+01:00', 'end' => '2026-11-06T11:00:00+01:00',
      'location' => 'Hall A', 'description' => str_repeat ( 'a', 62 ) . 'äää' . str_repeat ( 'b', 70 ) . "\nBring a laptop." ],
    [ 'title' => 'Dinner', 'date' => '2026-11-07', 'allday' => TRUE ],
    [ 'summary' => 'Walk', 'start' => '2026-11-08T14:00', 'uid' => 'walk@example.com', 'stamp' => '2026-10-01T12:00:00Z' ]
  ];

  $icsText = padFormatWrite ( 'ics', $icsRows, [ 'name' => 'Formats week' ] );
  $icsLong = max ( array_map ( 'strlen', explode ( "\r\n", $icsText ) ) );

  $icsShow = str_replace ( "\r\n", '~', $icsText );
  $icsSame = ( $icsText === padFormatWrite ( 'ics', $icsRows, [ 'name' => 'Formats week' ] ) ) ? 'the same bytes' : 'other bytes';
  $icsBack = implode ( ' | ', array_map ( fn ( $row ) => "{$row['summary']} {$row['start']} {$row['end']}", padFormatRead ( 'ics', $icsText ) ) );

?>
