<?php

  // date - a day of a month of a year, in any form date_parse reads without an error, that
  // checkdate takes: 2026-02-30 is none. The browser judges the ISO day and the day written
  // with / . or - itself, and leaves the other forms to the server.

  return [
    'check'   => function ( $value ) {
      $date = date_parse ( $value );
      return $date ['error_count'] == 0 and $date ['year'] !== FALSE and $date ['month'] !== FALSE and $date ['day'] !== FALSE
         and checkdate ( $date ['month'], $date ['day'], $date ['year'] );
    },
    'message' => ':label must be a date'
  ];

?>
