<?php

  // The region holds a {stack} that a {push} in it fills: the answer to the region's post
  // has the pushed item where the stack stands, as the full page has.

  $live = padCurl ( [ 'url'  => $padHost . 'regression/framework/?tags/a_page_with_a_stack_in_a_live_region&padInclude',
                      'post' => [ 'padLive' => 'box', 'padEvent' => 'add', 'padValue' => '41' ] ] );

  $answer = $live ['result'] . ': ' . $live ['data'];

?>
