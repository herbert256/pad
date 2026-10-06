<?php

  // A page asked for with padSample that has no sample is an error under the strict check.

  $missing = padCurl ( [ 'url' => $padGoExt . 'sample/plain&padSample&padInclude' ] );

  $missingResult = $missing ['result'] . ' ' . ( str_contains ( $missing ['data'], 'there is no sample for this page' ) ? 'named' : 'not named' );

?>
