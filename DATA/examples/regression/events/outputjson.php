<?php

  // The output hook sees the data answer as it goes out, and what it changes is sent: it was
  // handed the empty rendering the templates leave for a data answer, and the JSON went out
  // past it with the marker still in it. Said here as yes or no - this page's own output
  // passes the hook too.

  $outputData = json_decode ( padCurl ( $padGoExt . 'outputdata&padFormat=json' ) ['data'], TRUE ) ?? [];
  $outputSeen = ( ( $outputData ['note'] ?? '' ) === 'the output hook of the root ran' ) ? 'yes' : 'no';

?>
