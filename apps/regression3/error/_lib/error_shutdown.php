<?php

  // Nothing calls this. It exists to be included a second time by _tags/error_shutdown.php, which
  // redeclares it and so raises a fatal - the one kind of failure PHP will not let an error
  // handler see, and which the shutdown handler has to catch instead. That is what shutdown_1 and
  // shutdown_2 are for.

  function error_shutdown ( ) {

    return TRUE;

  }

?>