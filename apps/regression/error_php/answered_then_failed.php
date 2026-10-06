<?php

  // Fails once its answer has gone out: a shutdown function of the page raises an error
  // after the engine has sent the body and its Content-Length.

  register_shutdown_function ( function () { padError ( 'raised after the answer went out' ); } );

?>
