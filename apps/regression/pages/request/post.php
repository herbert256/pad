<?php

  // A real POST: the fields travel in the body, the url carries none, and the engine's
  // intake turns them into page globals for the fixture.

  $curl = padCurl ( [ 'url' => $padGoExt . 'request/vars&padInclude',
                      'post' => [ 'a' => 'hello', 'b' => 'twelve' ] ] );

  $postResult = $curl ['result'] . ' ' . padEscape ( trim ( $curl ['data'] ) );

?>
