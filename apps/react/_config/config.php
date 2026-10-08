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

  // _inits.pad writes the whole HTML page itself, so _common's page frame stays off - with
  // it on, the page was a second document inside _common's body, in quirks mode, and every
  // title was _common's page name.
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
