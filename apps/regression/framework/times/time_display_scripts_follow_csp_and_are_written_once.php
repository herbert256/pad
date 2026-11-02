<?php

$padCsp = "script-src 'nonce'";
$html = timeViewOutput ( "{time:clock timezone='UTC'/}{time:clock timezone='Asia/Tokyo'/}{time:stopwatch/}" );
$ok = substr_count ( $html, '<script nonce="' ) === 1 && str_contains ( $html, 'data-pad-clock="UTC"' )
   && str_contains ( $html, 'data-pad-clock="Asia/Tokyo"' ) && ! str_contains ( $html, 'onclick=' );
$answer = $ok ? 'one nonce-aware controller serves clocks and stopwatches' : 'FAIL';

?>
