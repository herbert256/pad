<?php

  // A full request stores the section that holds the region; the post of the region then
  // renders it again rather than serving the stored copy, which holds no {live} to answer.

  $liveUrl = $padHost . 'regression/framework/?tags/a_cached_section_with_a_live_counter&padInclude';

  padCurl ( [ 'url' => $liveUrl ] );

  $live = padCurl ( [ 'url' => $liveUrl, 'post' => [ 'padLive' => 'counter', 'padEvent' => 'add', 'padValue' => '41' ] ] );

  $answer = $live ['result'] . ': ' . $live ['data'];

?>
