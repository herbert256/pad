<?php

include_once APPS . 'tags/_lib/kinds.php';
$ok = ! is_file ( PAD . 'tags/timeline.php' ) && ! is_file ( PAD . 'tags/_lib/timeline.php' );
foreach ( padKinds ( 'timelines' ) as $kind ) {
  $example = tagsKindExample ( 'timeline', $kind );
  $html = timelineViewOutput ( $example ['source'] );
  $ok = $ok && ( str_contains ( $html, '<svg' ) || str_contains ( $html, 'pad-timeline-roadmap' ) );
}
$answer = $ok ? 'five timeline kinds render without the PAD tag' : 'FAIL';

?>
