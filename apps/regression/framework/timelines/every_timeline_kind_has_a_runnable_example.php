<?php

include_once APPS . 'tags/_lib/kinds.php';
$ok = ! is_file ( PAD . 'tags/timeline.php' ) && ! is_file ( PAD . 'tags/_lib/timeline.php' );
foreach ( padKinds ( 'timelines' ) as $kind ) {
  $example = tagsKindExample ( 'timeline', $kind );
  $exampleSource = $example ['source'];
  if ( $exampleSource === '' && $example ['page'] !== '' ) {
    $exampleFile = APPS . 'tags/' . $example ['page'];
    if ( is_file ( "$exampleFile.php" ) ) include "$exampleFile.php";
    $exampleSource = (string) file_get_contents ( "$exampleFile.pad" );
  }
  $html = timelineViewOutput ( $exampleSource );
  $view = [ 'roadmap' => 'pad-timeline-roadmap', 'audit' => 'pad-timeline-audit', 'rangecompare' => 'pad-rangecompare' ] [$kind] ?? '<svg';
  $ok = $ok && str_contains ( $html, $view );
  if ( $kind === 'audit' )
    $ok = $ok && str_contains ( $html, '<ol>' ) && str_contains ( $html, '<time datetime=' ) && str_contains ( $html, 'Changed title' );
  if ( $kind === 'rangecompare' )
    $ok = $ok && str_contains ( $html, 'planned:' ) && str_contains ( $html, 'actual:' ) && str_contains ( $html, 'missing' );
}
$answer = $ok ? 'seven timeline kinds render without the PAD tag' : 'FAIL';

?>
