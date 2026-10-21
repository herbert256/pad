<?php

  // The members fixture: every page below this directory is for the logged-in only - a guest
  // asked for one is sent to the login page, here the vars fixture. Included by a {page} of
  // a public page, the guard refuses and the part renders as nothing.

  padAuthRequire ( 'request/vars' );

?>
