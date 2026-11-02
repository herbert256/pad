<?php

  // A login below a {pad:flush}, on a session already open: the id cannot be renewed once the
  // headers have gone out, and the login was made on the id from before it - a fixation,
  // without a word. padLogin refuses it now.

  padSessionStart ();

?>
