<?php

  // A page that flashes and shows one kind only - {flash 'error'} - leaves the other kind in
  // the session for the next request: the success message it did not show is what the
  // following page shows. It used to be taken out with the shown one and was never seen.

  $typedOne = padCurl ( [ 'url' => $padGoExt . 'request/flashtyped&padInclude' ] );

  $typedJar = [ 'PHPSESSID' => $typedOne ['cookies'] ['PHPSESSID'] ?? '',
                'padFlash'  => $typedOne ['cookies'] ['padFlash']  ?? '' ];

  $typedTwo = padCurl ( [ 'url' => $padGoExt . 'request/flashed&padInclude', 'cookies' => $typedJar ] );

  $typedResult = trim ( $typedOne ['data'] ) . ' | ' . trim ( $typedTwo ['data'] );

?>
