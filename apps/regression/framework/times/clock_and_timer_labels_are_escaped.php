<?php

$html = timeViewOutput ( "{time:clock timezone='UTC', live=0, label='<img src=x onerror=alert(1)>'/}{time:stopwatch label='<script>alert(1)</script>'/}" );
$ok = ! str_contains ( $html, '<img' ) && ! str_contains ( $html, '<script>alert' )
   && str_contains ( $html, '&lt;img' ) && str_contains ( $html, '&lt;script&gt;' );
$answer = $ok ? 'clock and stopwatch labels are escaped' : 'FAIL';

?>
