<?php

  // An earlier request stores the named section with the bar chart; this request serves it
  // from the cache and draws the same chart beside it. The kept one was numbered by the
  // request that stored it - the first of its kind - and so was the one drawn now: one id
  // twice in the page. Kept, a chart's id is made with the section's key as well.

  if ( ! isset ( $warm ) ) {
    padFragmentForget ( 'fwChartSameIds' );
    padCurl ( [ 'url' => $padHost . "regression/framework/?tags/a_kept_chart_and_the_same_chart_drawn_now_have_their_own_ids&padInclude&warm=1" ] );
  }

?>
