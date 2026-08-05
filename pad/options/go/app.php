<?php

  // Runs the app phase of the option walk: the options of this tag for which the application
  // supplies its own handler in an _options/ directory, collected into $padOptionsAppStart
  // [$pad] while the parameters were parsed. Included by level/start.php, before the start
  // phase, and like it works on $padBase [$pad].

  $padOptions = 'app';

  include PAD . 'options/go/options.php';

?>