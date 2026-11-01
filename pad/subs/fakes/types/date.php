<?php

  // date - A moment between from= and to= - anything padDateParse reads: '2026-01-01',
  // '-1 year', a timestamp; a year ago to now unless said otherwise - written in format=,
  // 'Y-m-d' (padFakeDate). 'Y-m-d H:i:s' makes it a date and a time.

  return padFakeDate ( padFakeOpt ( 'from', '-1 year' ), padFakeOpt ( 'to', 'now' ), padFakeOpt ( 'format', 'Y-m-d' ) );

?>
