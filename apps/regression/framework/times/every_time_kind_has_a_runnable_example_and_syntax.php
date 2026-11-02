<?php

include_once APPS . 'tags/_lib/kinds.php';
$ok = ! is_file ( PAD . 'tags/countdown.php' ) && ! is_file ( PAD . 'tags/timeago.php' )
   && ! is_file ( PAD . 'tags/_lib/countdown.php' ) && ! is_file ( PAD . 'tags/_lib/timeago.php' );
foreach ( padKinds ( 'times' ) as $kind ) {
  $example = tagsKindExample ( 'time', $kind );
  $syntax = tagsKinds () ["time:$kind"] ['syntax'];
  $ok = $ok && trim ( timeViewOutput ( $example ['source'] ) ) !== '' && trim ( timeViewOutput ( $syntax ) ) !== '';
}
$answer = $ok ? 'six time kinds render without the old PAD tags' : 'FAIL';

?>
