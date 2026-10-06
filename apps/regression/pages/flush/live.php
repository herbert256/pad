<?php

  // The post of a live region is answered with the region's content alone, a {flush} above
  // the region notwithstanding: the flush sent the part of the page above it first, and the
  // region was swapped for the page's head followed by its own content.

  $flLive = padCurl ( [ 'url'  => $padGoExt . 'flush/livepage',
                        'post' => [ 'padLive' => 'box', 'padEvent' => 'go' ] ] );

  $flResult = $flLive ['result'] . ' ' . trim ( $flLive ['data'] );

?>
