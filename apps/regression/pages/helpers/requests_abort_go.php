<?php

  // The fixture of requests_abort: ends the request with the status and the message the
  // query names - 404 and its own phrase when it names none. What the page wrote before
  // is not sent.

  echo 'written before the abort';

  padAbort ( padRequest ( 'code', 404 ), padRequest ( 'message', '' ) );

?>
