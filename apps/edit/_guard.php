<?php

  // Who may use the editor - for every page of it, before anything else runs.
  //
  // The command line passes: pad test and pad lint run here, and whoever runs them can
  // change the files directly anyway. A web request must come from this machine (or from
  // anywhere, with $editRemote - editAllowed in _lib/api.php), and then be logged in: the
  // session names a user that still exists with the stamp it logged in with, so a deleted
  // user or a changed password ends the sessions that were open. The login page itself is
  // the one page open to a visitor who is not logged in; the JSON calls answer 403, which
  // the editor takes as "log in again"; any other page sends the visitor to the login.

  if ( PHP_SAPI === 'cli' )
    return TRUE;

  if ( ! editAllowed () )
    return FALSE;

  if ( $padPage == 'login' )
    return TRUE;

  if ( editUserValid ( editStore (), $editUser, $editStamp ) )
    return TRUE;

  if ( $padPage == 'api' )
    return FALSE;

  padRedirect ( 'login' );

?>
