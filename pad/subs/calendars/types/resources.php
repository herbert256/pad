<?php

  // resources - Timed bookings grouped by named resources, with timezone-aware intervals and overlap lanes.
  //

include_once PAD.'subs/calendars/_lib/timegrid.php';
return padCalendarTimegridTag(padTagParm('resources',[]));
?>
