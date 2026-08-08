<?php

  // {exit} ends the request from inside the template: padExit() runs the exit sequence and
  // never comes back. Nothing ships - the buffers are emptied, so the response is the status
  // code and an empty body, which the error/exit pages test pins. It is the way to stop -
  // PHP's exit and die must not be used in a PAD app.

  padExit ();

?>