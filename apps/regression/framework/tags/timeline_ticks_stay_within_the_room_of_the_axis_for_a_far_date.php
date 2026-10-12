<?php

  // A timeline from 1969 to a date far off - a millisecond timestamp read as seconds is the
  // year 57,700 - has no more ticks than its axis has room for (about one per 72 pixels):
  // the years' step stopped at 100, and there were hundreds of labels on top of each other,
  // or for a farther date a page of tens of megabytes and then no memory.

  $near  = padTimelineDate ( '1969' ) [0];
  $far   = padTimelineDate ( '1760000000000' ) [0];
  $ticks = count ( padTimelineTicks ( $near, $far, 648 ) );
  $room  = $ticks <= 10 ? 'within its room' : "$ticks ticks";

?>
