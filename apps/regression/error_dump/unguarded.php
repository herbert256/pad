<?php

  // With the try guards off a throwable reaches PHP's exception handler, after which PHP
  // ends the request whatever the handler does: the action records it and the request
  // ends as a failure - it ended on an empty 200, a blank page that said all was well.

  $padErrorTry = FALSE;

?>
