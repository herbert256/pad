<?php

  // An earlier request stores the named section with the bar chart, under the ids that
  // request gave it; this request serves the bar from the cache and draws the line itself.

  if ( ! isset ( $warm ) ) {
    padFragmentForget ( 'fwChartIds' );
    padCurl ( [ 'url' => $padHost . "regression/framework/?tags/a_chart_from_the_fragment_cache_and_a_chart_drawn_now_have_their_own_ids&padInclude&warm=1" ] );
  }

?>
