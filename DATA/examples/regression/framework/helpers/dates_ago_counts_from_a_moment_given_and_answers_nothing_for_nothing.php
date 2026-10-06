<?php

  // A second argument is the moment to count from instead of now; both arguments take
  // whatever padDateParse takes. The calendar is the one of that moment's zone: 26 hours
  // are yesterday in UTC and two days ago in Tokyo. NULL and '' have no age: ''.

  $padTimezone = 'UTC';

  $given = [
    padAgo ( '2026-01-01', '2026-01-02' ),
    padAgo ( 1768464000, 1768471200 ),
    padAgo ( new DateTime ( '2026-01-15 08:00:00' ), new DateTimeImmutable ( '2026-01-15 10:00:00' ) ),
    padAgo ( '2026-01-15 14:00:00+00:00', '2026-01-16T16:00:00+00:00' ),
    padAgo ( '2026-01-15 14:00:00+00:00', '2026-01-17T01:00:00+09:00' ),
    padAgo ( '2026-01-02', '2026-01-01' ),
    '[' . padAgo ( NULL ) . ']',
    '[' . padAgo ( '' ) . ']'
  ];

  $given = implode ( ' / ', $given );

?>
