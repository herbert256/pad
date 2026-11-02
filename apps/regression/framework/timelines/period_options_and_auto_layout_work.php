<?php

$rows = '[{"date":"2025-12-01","label":"Old"},{"date":"2026-01-01","label":"New"}]';
$year = timelineViewOutput ( "{timeline:roadmap period='year', date='date', label='label'}$rows{/timeline:roadmap}" );
$month = timelineViewOutput ( "{timeline:roadmap period='month', date='date', label='label'}$rows{/timeline:roadmap}" );
$auto = timelineViewOutput ( "{timeline:auto vertical, date='date', label='label'}$rows{/timeline:auto}" );
$vertical = timelineViewOutput ( "{timeline:vertical date='date', label='label'}$rows{/timeline:vertical}" );
$normalize = fn ( $html ) => preg_replace ( '/pad-chart-[a-f0-9]+(?:-[0-9]+)?/', 'chart', $html );
$ok = $normalize ( $auto ) === $normalize ( $vertical ) && str_contains ( $year, 'data-period="2025"' ) && str_contains ( $year, 'data-period="2026"' )
   && str_contains ( $month, 'data-period="2025-12"' ) && str_contains ( $month, 'data-period="2026-01"' );
$answer = $ok ? 'period selectors and runtime layout work' : 'FAIL';

?>
