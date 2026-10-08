<?php

  // The admin console reads and changes the whole PAD installation - it takes applications
  // down, empties caches and queues, runs the pad command - so it is kept as close as the
  // editor is:
  //
  // - a login, always: _guard.php lets nobody past without the session of a user that still
  //   exists in DATA/admin/users.json, with the stamp that user had when they logged in
  // - this machine only, unless $adminRemote says otherwise: the request comes from the
  //   loopback address with nothing forwarded, naming this machine in its Host header
  // - the CSRF token on every post
  // - no request value turned into a variable: the pages ask padRequest for what they read
  //
  // This file is read twice per request (inits/config.php): assignments only.

  $padCommon      = FALSE;
  $padCsrf        = TRUE;
  $padRequestVars = [];
  $padSessionVars = [ 'adminUser', 'adminStamp' ];

  // TRUE lets a request from another machine log in; $adminHosts adds host names, besides
  // localhost, 127.0.0.1 and [::1], that a request may name in its Host header.

  $adminRemote = FALSE;
  $adminHosts  = [];

  // The console's command page runs the pad command (apps/cli/pad) as the web server's
  // user; FALSE leaves only the pages that read. $adminTimeout is the longest one command
  // may run, in seconds.

  $adminCommands = TRUE;
  $adminTimeout  = 120;

  // The largest file the files page shows, and how many lines of a log the logs page shows.

  $adminMaxFile = 512 * 1024;
  $adminLogTail = 400;

?>
