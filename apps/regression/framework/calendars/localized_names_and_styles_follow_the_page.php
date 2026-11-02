<?php

$padLocale = 'nl_NL';
  $html = calendarViewOutput ( '{calendar:day "2026-11-04"/}{calendar:day "2026-11-05"/}' );
  $answer = substr_count ( $html, '<style' ) === 1 && str_contains ( $html, 'woensdag 4 november 2026' )
           ? 'Dutch dates and one view stylesheet' : 'FAIL';

?>
