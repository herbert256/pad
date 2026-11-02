<?php

$html = timeViewOutput ( "{time:stopwatch 65.2, label='Practice'/}{time:stopwatch autostart, label='Reading'/}" );
$ok = substr_count ( $html, 'data-pad-stopwatch=' ) === 2 && substr_count ( $html, '<script' ) === 1
   && str_contains ( $html, '>00:01:05.2</output>' ) && str_contains ( $html, 'data-pad-running="1"' )
   && substr_count ( $html, 'type="button"' ) === 6 && substr_count ( $html, 'role="timer" aria-live="off"' ) === 2
   && substr_count ( $html, 'role="status"' ) === 2 && ! str_contains ( $html, 'onclick=' );
$answer = $ok ? 'stopwatches have independent accessible controls' : 'FAIL';

?>
