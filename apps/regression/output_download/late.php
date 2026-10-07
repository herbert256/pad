<?php

  // The fixture of lateread: a download that fails once it has gone out - a shutdown
  // function warns, and another raises a PAD error.

  register_shutdown_function ( function () { trigger_error ( 'warned after the download went out', E_USER_WARNING ); } );
  register_shutdown_function ( function () { padError ( 'a PAD error after the download went out' ); } );

?>
