<?php

$rows = '[{"date":"2026-06-18","label":"Launch","team":"Marketing","mark":true},{"date":"2026-01-12","label":"Draft","team":"Engineering","mark":false},{"date":"2026-03-01","label":"Preview","team":"Engineering","mark":true}]';
$html = timelineViewOutput ( "{timeline:roadmap date='date', label='label', lane='team', milestone='mark'}$rows{/timeline:roadmap}" );
$ok = substr_count ( $html, 'data-period=' ) === 2 && strpos ( $html, '2026-Q1' ) < strpos ( $html, '2026-Q2' )
   && substr_count ( $html, 'class="pt-milestone"' ) === 2 && strpos ( $html, 'Draft' ) < strpos ( $html, 'Preview' )
   && str_contains ( $html, '<h4>Engineering</h4>' ) && str_contains ( $html, '<h4>Marketing</h4>' );
$answer = $ok ? 'roadmap periods, lanes and milestones preserved' : 'FAIL';

?>
