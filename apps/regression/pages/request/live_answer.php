<?php

  // A live event is answered with the inner content of its region, and nothing else: the
  // debug toolbar, the live reload script and the output check's panel - all on for the
  // page - stay out of it. They were added to the answer as to a whole page, and every
  // click put another toolbar inside the region.

  $laAnswer = padCurl ( [ 'url'  => $padGoExt . 'request/livebar',
                          'post' => [ 'padLive' => 'bar', 'padEvent' => 'go' ] ] );

  $laResult = $laAnswer ['result'] . ' ' . trim ( $laAnswer ['data'] );

?>
