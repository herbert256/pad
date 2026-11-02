<?php

$before = timeViewOutput ( "{time:clock timezone='Europe/Amsterdam', now='2026-10-25T00:30:00Z', format='Y-m-d H:i:s', live=0/}" );
$after = timeViewOutput ( "{time:clock timezone='Europe/Amsterdam', now='2026-10-25T01:30:00Z', format='Y-m-d H:i:s', live=0/}" );
$tokyo = timeViewOutput ( "{time:clock timezone='Asia/Tokyo', now='2026-11-02T18:30:00Z', format='Y-m-d H:i:s', live=0/}" );
$ok = str_contains ( $before, '2026-10-25T02:30:00+02:00' ) && str_contains ( $after, '2026-10-25T02:30:00+01:00' )
   && str_contains ( $tokyo, '>2026-11-03 03:30:00</time>' ) && ! str_contains ( $tokyo, '<script' );
$answer = $ok ? 'world clocks preserve daylight saving and local dates' : 'FAIL';

?>
