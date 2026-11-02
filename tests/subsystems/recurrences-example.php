<?php
// With PAD loaded, feed these starts into application event rows.
$starts = padOccurrences ( 'weekly', [ 'start' => '2026-11-02T09:00:00', 'timezone' => 'Europe/Amsterdam', 'byday' => [ 'MO', 'WE' ], 'count' => 4 ], '2026-11-01', '2026-12-01' );
?>
