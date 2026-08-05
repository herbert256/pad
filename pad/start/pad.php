<?php

  // The engine's boot sequence, reached from pad.php once the path constants exist.
  //
  // Error reporting is armed first, so that anything failing from here on is caught and
  // reported rather than half-printed: error/claude.php for the JSON reply local curl
  // clients get, error/boot.php for the error, exception and shutdown handlers. Then come
  // the framework's default settings from config/config.php, and finally start/pad/go.php,
  // which runs the actual request.

  include PAD . 'error/claude.php';
  include PAD . 'error/boot.php';
  include PAD . 'config/config.php';
  include PAD . 'start/pad/go.php';

?>