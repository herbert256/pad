<?php

padNowFreeze ( '2026-11-04 12:00:00' );
  $_GET ['day'] = [ 'bad' ];
  $list = calendarViewOutput ( '{calendar:day}{$date}{/calendar:day}' );
  $_GET ['day'] = '2026-02-30';
  $bad = calendarViewOutput ( '{calendar:day}{$date}{/calendar:day}' );
  $answer = trim ( $list ) === '2026-11-04' && trim ( $bad ) === '2026-11-04' ? 'invalid request values fall back without warnings' : 'FAIL';

?>
