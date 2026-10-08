<?php

  // Who may use the console - for every page of it, before anything else runs.
  //
  // The command line passes: pad lint and pad test run here, and whoever runs them can read
  // and change DATA/ directly anyway. A web request must come from this machine (or from
  // anywhere, with $adminRemote - adminAllowed in _lib/admin.php) and then be logged in: the
  // session names a user that still exists with the stamp it logged in with, so a deleted
  // user or a changed password ends the sessions that were open. The login page is the one
  // page open to a visitor who is not logged in; any other page sends them there.

  if ( PHP_SAPI === 'cli' )
    return TRUE;

  if ( ! adminAllowed () )
    return FALSE;

  if ( $padPage == 'login' )
    return TRUE;

  if ( adminUserValid ( $adminUser, $adminStamp ) )
    return TRUE;

  padRedirect ( 'login' );

?>
