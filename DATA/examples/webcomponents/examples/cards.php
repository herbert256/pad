<?php

  // Three cards from one custom tag: {card} writes a <pad-card> with its shadow root, the
  // stylesheet inlined by {shadow css=} and named slots; the page hands it the title and the
  // tone as parameters and the rest as content - the light DOM the browser slots in.

  $cards = [ [ 'title' => 'Revenue',  'tone' => 'good', 'value' => '€ 48,210', 'note' => '12% more than last month' ],
             [ 'title' => 'Visitors', 'tone' => 'info', 'value' => '12,804',   'note' => 'Most of them on a phone' ],
             [ 'title' => 'Returns',  'tone' => 'bad',  'value' => '38',       'note' => 'Three more than usual' ] ];

?>
