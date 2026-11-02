<?php

$rows = '[{"date":"2026-01-01","label":"<script>alert(1)</script>","team":"<img src=x onerror=alert(1)>","note":"A&B"}]';
$ok = TRUE;
foreach ( [ 'roadmap', 'swimlane' ] as $kind ) {
  $html = timelineViewOutput ( "{timeline:$kind date='date', label='label', text='note', lane='team'}$rows{/timeline:$kind}" );
  $ok = $ok && ! str_contains ( $html, '<script>' ) && ! str_contains ( $html, '<img' )
      && str_contains ( $html, '&lt;script&gt;' ) && str_contains ( $html, '&amp;' );
}
$answer = $ok ? 'all timeline text is escaped' : 'FAIL';

?>
