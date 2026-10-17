<?php

  // A moment - a Unix timestamp, a date with its offset - falls on the day it is in the
  // application's timezone, the one today is: an event at local midnight in Amsterdam is
  // on the 13th, where the calendar put a timestamp and an offset date on their UTC day,
  // the 12th, and the timeline wrote them as 22:00 and 22:30 of the 12th.

  include_once PAD . 'tags/_lib/timeline.php';

  $padTimezone = 'Europe/Amsterdam';

  padNowFreeze ( '2025-10-13 00:30:00' );

  $agenda = [ [ 'when' => '1760306400',                'what' => 'stamp'  ],
              [ 'when' => '2025-10-13T00:30:00+02:00', 'what' => 'offset' ],
              [ 'when' => '2025-10-13 00:30:00',       'what' => 'plain'  ] ];

  $shown = padTimelineShow ( ...padTimelineDate ( '1760306400' ) );

?>
