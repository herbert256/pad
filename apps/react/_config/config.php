<?php

  // Application configuration

  // Output type (web, file, download, console)
  $padOutputType = 'web';

  // Error handling (pad, boot, php, stop, exit, ignore, log, dump)
  $padErrorAction = 'pad';

  // Debug info (trace, stats, track, xml, xref)
  // $padInfo = 'trace';

  // Tidy HTML output
  $padTidy = FALSE;

  // _common's parts stay off - its tags, and its title from the page's name. The frame of
  // the PAD applications is reached all the same, {common:padFrame} in _inits.pad.
  $padCommon = FALSE;

  // CSRF protection: a post without the session's token is answered 403 before the page
  // runs. The wrapper writes the token into <meta name="csrf-token">, and PadReact.post
  // (www/react/pad-react.js) sends it back in an X-CSRF-Token header.
  $padCsrf = TRUE;

  $padSqlHost     = padEnv ( 'padSqlHost' );
  $padSqlDatabase = padEnv ( 'padSqlDatabase' );
  $padSqlUser     = padEnv ( 'padSqlUser' );
  $padSqlPassword = padEnv ( 'padSqlPassword' );

?>
