<?php

  // ?month[]=x - a list where the calendar reads a month from the request - is no month,
  // as any other value that is none: the calendar shows this month, where turning the list
  // into text ended the request of every page with a {calendar}, for any visitor.

  $_GET ['month'] = [ 'x' ];

  padNowFreeze ( '2026-10-12 12:00:00' );

?>
