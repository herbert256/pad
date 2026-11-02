<?php

$padTimezone = 'Europe/Amsterdam';
  padNowFreeze ( '2025-10-13 00:30:00' );
  $items = [ [ 'when' => new DateTimeImmutable ( '2025-10-12T22:30:00Z' ), 'what' => 'Object' ],
             [ 'when' => '2025-10-13T00:45:00+02:00', 'what' => 'Offset' ] ];
  $rows = calendarViewOutput ( '{calendar:day "2025-10-13", data="items"}{$date}:{$count}:{$today}:{level:events}{$what},{/level:events}{/calendar:day}' );
  $html = calendarViewOutput ( '{calendar:day "2025-10-13", data="items"/}' );
  $answer = trim ( $rows ) === '2025-10-13:2:1:Object,Offset,' && substr_count ( $html, '<section ' ) === 1
         && substr_count ( $html, '<article ' ) === 1 && str_contains ( $html, 'aria-current="date"' )
           ? 'both moments on their local day, one rendered view' : 'FAIL';

?>
