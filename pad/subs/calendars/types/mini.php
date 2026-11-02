<?php

  // mini - a compact month for a sidebar, with the same events, holidays and pair rows as month.
  // The parameter is YYYY-MM; query defaults to month.

  $padCalendarMini = include PAD . 'subs/calendars/types/month.php';

  return $padPair [$pad] ? $padCalendarMini
    : padCalendarViewsStyle () . '<div class="pad-calendar-mini">' . $padCalendarMini . '</div>';

?>
