<?php

  // Fails once its answer has gone out with an error of its own, not padError: a shutdown
  // function throws, and another warns.

  register_shutdown_function ( function () { trigger_error ( 'warned after the answer went out', E_USER_WARNING ); } );
  register_shutdown_function ( function () { throw new Exception ( 'thrown after the answer went out' ); } );

?>
