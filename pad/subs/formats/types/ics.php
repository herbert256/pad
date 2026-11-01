<?php

  // ics - iCalendar (RFC 5545), read and written: the VEVENTs of a calendar as rows - uid,
  // summary, start, end, allday, timezone, location, description, url, status, categories,
  // rrule - and rows as a calendar, folded at 75 octets with CRLF line ends, its UID and
  // DTSTAMP made of the rows so the same rows give the same bytes. name= names the calendar
  // (X-WR-CALNAME), stamp= is the DTSTAMP of the rows without one. Known on sight: a text
  // that opens with BEGIN:VCALENDAR.

  return [
    'name'      => 'iCalendar',
    'mime'      => 'text/calendar',
    'extension' => [ 'ics', 'ical', 'ifb', 'icalendar' ],
    'shape'     => 'rows',
    'lib'       => 'ics',
    'read'      => fn ( $text, $options ) => padIcsRead ( $text ),
    'write'     => fn ( $data, $options ) => padIcsWrite ( padFormatRows ( $data ), $options ),
    'sniff'     => [ 'csv', fn ( $text ) => (bool) preg_match ( '/\ABEGIN:VCALENDAR[ \t]*\r?\n/i', $text ) ],
    'download'  => 'inline'
  ];

?>
