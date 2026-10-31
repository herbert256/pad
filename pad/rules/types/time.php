<?php

  // time - a time of day on the 24-hour clock, HH:MM or HH:MM:SS, the hour with one digit or
  // two: what <input type="time"> posts, 09:30, and what a person types, 9:30.

  return [
    'check'   => fn ( $value ) => preg_match ( '/^([01]?\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/D', $value ) === 1,
    'message' => ':label must be a time'
  ];

?>
