<?php

  // The month a {calendar} shows, read from a text - the part of the tag that a page's PHP
  // uses too, to know the month the calendar's links ask for (apps/demo/calendar.php). The
  // rest of the tag is tags/_lib/calendar.php, loaded when a page uses it.
  //
  // 'YYYY-MM' of a text, or NULL - for what is no text too: ?month[]=x in the request was
  // turned into text, and the warning ended the request of every page with a {calendar}.

  function padCalendarMonth ( $text ) {

    $text = trim ( is_scalar ( $text ) ? (string) $text : '' );

    if ( ! preg_match ( '/^(\d{4})-(\d{1,2})$/', $text, $m ) or $m [2] < 1 or $m [2] > 12 )
      return NULL;

    return sprintf ( '%04d-%02d', $m [1], $m [2] );

  }

?>
