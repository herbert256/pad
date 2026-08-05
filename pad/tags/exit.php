<?php

  // {exit} ends the request from inside the template: padExit() sends what has been built
  // so far, runs the exit sequence and never comes back, so the rest of the page is not
  // processed. It is the way to stop - PHP's exit and die must not be used in a PAD app.

  padExit ();

?>