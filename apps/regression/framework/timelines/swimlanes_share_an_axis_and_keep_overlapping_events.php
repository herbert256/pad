<?php

$rows = '[{"start":"2026-01-01","end":"2026-02-01","label":"First","team":"A"},{"start":"2026-01-01","end":"2026-02-01","label":"Second","team":"A"},{"date":"2026-02-01","label":"Launch","team":"B"}]';
$html = timelineViewOutput ( "{timeline:swimlane date='date', from='start', to='end', label='label', lane='team'}$rows{/timeline:swimlane}" );
$ok = substr_count ( $html, 'class="pt-lane"' ) === 2 && substr_count ( $html, 'class="pc-mark"' ) === 3
   && substr_count ( $html, 'class="pt-milestone ' ) === 1 && substr_count ( $html, 'height="12"' ) === 2
   && str_contains ( $html, 'aria-label="A"' ) && str_contains ( $html, 'aria-label="B"' );
$answer = $ok ? 'swimlanes keep three events in two lanes' : 'FAIL';

?>
