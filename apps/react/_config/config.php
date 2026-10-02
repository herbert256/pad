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

  $padSqlHost     = '127.0.0.1';
  $padSqlDatabase = 'support';
  $padSqlUser     = 'support';
  $padSqlPassword = 'support';
  
?>