<?php

  // The icon at the top right of an example is a link to the playground with the example in
  // its boxes, in the #s= hash the playground keeps them in: the template, and its PHP or its
  // data. An {example} of the manual with a paired PHP file, and a chart of the charts
  // application whose data is the _data file its tag names - given under that name.

  $boxes = function ( $url ) {

    $page = padCurl ( $url ) ['data'];

    if ( ! preg_match ( '#<a class="pad-play[^"]*" href="[^"]*/playground/\#s=([A-Za-z0-9+/=]+)"#', $page, $m ) )
      return 'no icon';

    $box = json_decode ( base64_decode ( $m [1] ), TRUE );

    return $box ['mode'] . ' | ' . strtok ( $box ['source'], "\n" ) . ' | ' . strtok ( $box ['php'] ?? $box ['data'], "\n" );

  };

  $answer = $boxes ( $padHost . 'manual/?pages/arrays&padInclude' ) . ' // '
          . $boxes ( $padHost . 'charts/?bars/energy' );

?>
