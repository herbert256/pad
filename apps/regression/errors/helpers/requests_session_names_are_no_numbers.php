<?php

  // PHP's session drops a top-level name that is a number when it writes the session - padSessionPut says so instead.

  padSessionPut ( 5, 'five' );

?>
