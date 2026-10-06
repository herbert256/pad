<?php

  $liveUrl = $padHost . 'regression/framework/?tags/a_page_with_a_live_counter&padInclude';

  $liveHit  = padCurl ( [ 'url' => $liveUrl, 'post' => [ 'padLive' => 'counter', 'padEvent' => 'add', 'padValue' => '41' ] ] );
  $liveMiss = padCurl ( [ 'url' => $liveUrl, 'post' => [ 'padLive' => 'basket',  'padEvent' => 'add' ] ] );

  $hit  = $liveHit ['result'] . ': ' . $liveHit ['data'];
  $miss = $liveMiss ['result'] . ': ' . ( preg_match ( "/there is no live region .{1,6}basket/", $liveMiss ['data'] ) ? 'no such region' : 'answered' );

?>
