<?php

  $html = calendarViewOutput ( "{calendar:month '2001-02'/}" );
  $week = '{$week}:{array:days}{$day}{pad:if $other}*{/pad:if} {/array:days}|';
  $month = calendarViewOutput ( "{calendar:month '2001-02'}$week{/calendar:month}" );
  $mini = calendarViewOutput ( "{calendar:mini '2001-02'}$week{/calendar:mini}" );
  $rows = '5:29* 30* 31* 1 2 3 4 |6:5 6 7 8 9 10 11 |7:12 13 14 15 16 17 18 |8:19 20 21 22 23 24 25 |9:26 27 28 1* 2* 3* 4* |';
  $answer = str_contains ( $html, '<table class="pad-calendar"><caption><span>February 2001</span></caption>' )
         && substr_count ( $html, '<time datetime=' ) === 35
         && str_contains ( $html, '<time datetime="2001-01-29">29</time>' )
         && str_contains ( $html, '<time datetime="2001-03-04">4</time>' )
         && $month === $rows && $mini === $rows ? 'month markup and both row forms preserved' : 'FAIL';

?>
