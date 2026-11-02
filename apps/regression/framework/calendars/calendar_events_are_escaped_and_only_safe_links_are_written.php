<?php

$items = [ [ 'when' => '2026-11-04', 'what' => '<b>Review</b>', 'url' => 'https://example.com/?x=1&y=2' ],
             [ 'when' => '2026-11-04', 'what' => '{php:phpversion/}', 'url' => 'javascript:alert(1)' ],
             [ 'when' => '2026-11-04', 'what' => 'Bad', 'url' => 'data:text/html,evil' ] ];
  $checks = [];
  foreach ( [ 'day' => '2026-11-04', 'week' => '2026-11-04', 'agenda' => '2026-11', 'quarter' => '2026-Q4', 'year' => '2026' ] as $view => $date ) {
    $html = calendarViewOutput ( "{calendar:$view '$date', data='items', link='url'/}" );
    $checks [] = str_contains ( $html, '&lt;b&gt;Review&lt;/b&gt;' ) && str_contains ( $html, '{php:phpversion/}' )
              && str_contains ( $html, 'href="https://example.com/?x=1&amp;y=2"' )
              && ! str_contains ( $html, 'href="javascript:' ) && ! str_contains ( $html, 'href="data:' );
  }
  $answer = ! in_array ( FALSE, $checks, TRUE ) ? 'all new views escape text and reject script links' : 'FAIL';

?>
