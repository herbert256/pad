<?php

  // A top-level session name with a | in it makes PHP's session refuse to write the whole session - every value of it lost, silently, at the end of the request - padSessionPut says so instead.

  padSessionPut ( 'seen|42', TRUE );

?>
