<?php

  // One complete run of the engine over a page: set everything up, process the page level
  // by level, then finish the response. Reached from start/pad.php once per request, and
  // again from start/restart.php each time a run is abandoned in favour of another page.

  include PAD . 'inits/inits.php';
  include PAD . 'start/pad/level.php';
  include PAD . 'exits/exits.php';

?>