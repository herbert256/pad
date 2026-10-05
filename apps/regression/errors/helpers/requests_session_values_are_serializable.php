<?php

  // A Closure cannot be serialized, and a session that holds one is not written at all at
  // the end of the request - padSessionPut refuses it by name instead.

  padSessionPut ( 'callback', fn () => 1 );

?>
