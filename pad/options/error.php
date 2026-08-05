<?php

  // Handler for the error option, documented as an alias of notOk - but it resets on the notOk
  // parameter, and nothing includes this file (level/flags.php and try/catch/level/go.php go
  // straight to options/notOk.php), so error="..." is not actually wired up.

  $padReset = 'notOk';

  return include PAD . 'options/go/reset.php';

?>