<?php

  // A date the formats read is a moment of its own: an offset past 14 hours or 59 minutes is
  // no offset, and a text whose moment is the clock's - yesterday, a time of today, a day of
  // this year - is no date, so the feed writes those entries without one, where +9999 ended
  // the feed on DateInvalidTimeZoneException, +99:00 moved the moment by days and yesterday
  // wrote another feed every day; a zone that is no zone - a null byte in it - says nothing,
  // the event's local time floating, where it was a ValueError. 2026-11-01T10:00:00+14:00
  // and an RFC 2822 date stay the moments they are.

  $dateRss = padFormatWrite ( 'rss', [
    [ 'title' => 'far',       'date' => '2026-11-01 10:00+9999' ],
    [ 'title' => 'days',      'date' => '2026-11-01T10:00:00+99:00' ],
    [ 'title' => 'minutes',   'date' => '2026-11-01T10:00:00+01:75' ],
    [ 'title' => 'yesterday', 'date' => 'yesterday' ],
    [ 'title' => 'today',     'date' => '10:00' ],
    [ 'title' => 'this year', 'date' => 'Nov 1' ],
    [ 'title' => 'kiribati',  'date' => '2026-11-01T10:00:00+14:00' ],
    [ 'title' => 'rfc',       'date' => 'Sun, 01 Nov 2026 10:00:00 +0100' ]
  ], [ 'title' => 'Dates', 'link' => 'https://example.com/' ] );

  preg_match_all ( '#<title>([^<]*)</title>(?:\s*<link>[^<]*</link>)?(?:\s*<pubDate>([^<]*)</pubDate>)?#', $dateRss, $dateHits, PREG_SET_ORDER );

  $dateShow = implode ( ' | ', array_map ( fn ( $hit ) => $hit [1] . ' ' . ( $hit [2] ?? '-' ), array_slice ( $dateHits, 1 ) ) );

  $dateIcs = padFormatWrite ( 'ics', [ [ 'summary' => 'zone', 'start' => '2026-11-01T10:00', 'timezone' => "Europe/Amste\0rdam" ] ] );
  $dateIcs = preg_match ( '/DTSTART[^\r]*/', $dateIcs, $dateHit ) ? $dateHit [0] : 'none';

?>
