<?php

  padNowFreeze ( '2026-10-05 14:30:00' );

  $now       = padNow ( 'l j F Y, H:i' );
  $today     = padToday ( 'Y-m-d H:i:s' );
  $christmas = padDateParse ( '2026-12-25' )->format ( 'l j F' );
  $nextWeek  = padDateParse ( '+1 week' )->format ( 'Y-m-d' );
  $nothing   = ( padDateParse ( '2026-02-30' ) === NULL )
             ? 'NULL' : 'a date';

?>
