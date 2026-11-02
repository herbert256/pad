<?php

$items = [ [ 'when' => '2024-02-29', 'what' => 'Last' ], [ 'when' => '2024-02-03', 'what' => 'First' ],
             [ 'when' => '2024-03-01', 'what' => 'Outside' ], [ 'when' => '2024-02-03', 'what' => 'Second' ] ];
  $agenda = calendarViewOutput ( '{calendar:agenda "2024-02", data="items"}{$date}:{$count}:{level:events}{$what},{/level:events}|{/calendar:agenda}' );
  $all = explode ( ' ', trim ( calendarViewOutput ( '{calendar:agenda "2024-02", empty}{$date} {/calendar:agenda}' ) ) );
  $none = calendarViewOutput ( '{calendar:agenda "2024-02"}unexpected@else@empty{/calendar:agenda}' );
  $answer = trim ( $agenda ) === '2024-02-03:2:First,Second,|2024-02-29:1:Last,|'
         && count ( $all ) === 29 && trim ( $none ) === 'empty'
           ? 'agenda dates sorted, events stable, 29 days with empty, else without events' : 'FAIL';

?>
