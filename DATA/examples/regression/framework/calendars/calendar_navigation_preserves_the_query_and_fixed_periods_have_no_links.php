<?php

$_GET ['schedule'] = '2026-12-31';
  $_GET ['keep'] = 'yes';
  $html = html_entity_decode ( calendarViewOutput ( '{calendar:week query="schedule"/}' ) );
  $fixed = calendarViewOutput ( '{calendar:week "2026-11-04", query="schedule"/}' );
  $follow = calendarViewOutput ( '{calendar:week "2026-12-31", query="schedule"/}' );
  $answer = str_contains ( $html, 'schedule=2026-12-21' ) && str_contains ( $html, 'schedule=2027-01-04' )
         && str_contains ( $html, 'keep=yes' ) && ! str_contains ( $fixed, 'rel="next"' )
         && str_contains ( $follow, 'rel="next"' ) ? 'period links preserve query values and respect fixed dates' : 'FAIL';

?>
