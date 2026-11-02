<?php

  // calendar:kind selects a Gregorian calendar view from subs/calendars/types/.
  // Its first parameter selects the period; a pair hands its rows to the template.

  $padCalendarKind = $padTag [$pad];
  $padTagGo = PAD . 'subs/calendars/calendar';

  return include PAD . 'types/_go/tag.php';

?>
