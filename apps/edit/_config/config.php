<?php

  // The editor writes the files of every application - .php files among them - so it is
  // code execution on this machine by design, and is kept as close as the engine allows:
  //
  // - a login, always: _guard.php lets nobody past who has no session of a user that still
  //   exists in DATA/edit/users.json, with the stamp that user had when they logged in
  // - this machine only, unless $editRemote says otherwise: the request must come from the
  //   loopback address with nothing forwarded (padLoopback), and name this machine in its
  //   Host header - a page on another site that rebinds its own name to 127.0.0.1 reaches
  //   the loopback address too, but not with Host: localhost
  // - the CSRF token on every post, the JSON calls included (X-CSRF-Token)
  // - no request value turned into a variable: the calls read their JSON body themselves,
  //   untrimmed, since a file's leading and trailing whitespace is part of it
  //
  // This file is read twice per request (inits/config.php): assignments only.

  $padCommon      = FALSE;
  $padCsrf        = TRUE;
  $padRequestVars = [];
  $padTidy        = FALSE;
  $padSessionVars = [ 'editUser', 'editStamp' ];

  // TRUE lets a request from another machine log in; $editHosts adds host names, besides
  // localhost, 127.0.0.1 and [::1], that a request may name in its Host header.

  $editRemote = FALSE;
  $editHosts  = [];

  // The editor component: Monaco's AMD build. A copy of its min/vs directory under www/
  // works as well - '/pad/edit/monaco/vs', say - for a machine without internet access.

  $editMonaco = 'https://cdn.jsdelivr.net/npm/monaco-editor@0.57.0/min/vs';

  // How many earlier versions of a file the history keeps, and how many days a deleted
  // file stays in the trash.

  $editHistoryKeep = 50;
  $editTrashDays   = 30;

  // The largest file the editor opens as text.

  $editMaxText = 2 * 1024 * 1024;

  // The terminal in the editor's bottom panel runs commands in a shell as the web server's
  // user. FALSE switches it off; $editShell names the shell, '' takes bash, zsh or sh.

  $editTerminal = TRUE;
  $editShell    = '';

  // The step debugger: Xdebug in the request being debugged connects to a debugger
  // process the editor starts, on xdebug.client_port (9003). FALSE switches it off.

  $editDebug = TRUE;

?>
