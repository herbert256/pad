<?php

  // The fixture of session_foreign: a session value under a name this application never
  // declared, as another application on the host - they share the session cookie - or a
  // helper keeping its own state leaves one.

  padSessionStart ();

  $_SESSION ['foreignAdmin'] = 'yes';

?>
