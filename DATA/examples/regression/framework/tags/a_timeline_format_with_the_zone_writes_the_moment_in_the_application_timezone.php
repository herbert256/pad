<?php

  // A format= that names the zone or the epoch writes a timestamp or a date with its offset
  // as the moment it is in the application's timezone - its zone's name, offset and the
  // real timestamp - where the wall clock was written as if in UTC: midnight in Amsterdam
  // said 00:00 GMT (+00:00), and U was the timestamp plus two hours. A date written without
  // a zone has none to name and is written in UTC, as before.

  $padTimezone = 'Europe/Amsterdam';

  $agenda = [ [ 'when' => '1760306400',                'what' => 'stamp'  ],
              [ 'when' => '2025-10-13T00:30:00+02:00', 'what' => 'offset' ],
              [ 'when' => '2025-10-13 00:30:00',       'what' => 'plain'  ] ];

?>
