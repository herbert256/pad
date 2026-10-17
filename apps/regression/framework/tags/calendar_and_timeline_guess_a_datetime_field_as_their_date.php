<?php

  // A field holding a date object of the page's PHP is guessed as the date field, as a
  // field holding a date as text is: read only when date= named it, the timeline without
  // date= failed - "the timeline has no events with a date" - and the calendar showed no
  // event at all, the text date of the other row neither.

  $agenda = [ [ 'when' => new DateTimeImmutable ( '2026-10-05 10:00', new DateTimeZone ( 'UTC' ) ), 'what' => 'Review' ],
              [ 'when' => '2026-10-07', 'what' => 'Text date' ] ];

?>
