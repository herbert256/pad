<?php

  // The terminal (_lib/terminal.php): run a command, read what it wrote, stop it, complete
  // a name with Tab.

  global $editUser, $editTerminal;

  if ( ! $editTerminal )
    editFail ( 'the terminal is switched off: $editTerminal in _config/config.php' );

  $base = editTermBase ();
  $user = $editUser ?: 'cli';

  switch ( editArg ( $body, 'op' ) ) {

    case 'run':
      $cwd = editTermCwd ( editArg ( $body, 'cwd' ), editArg ( $body, 'app' ) );
      $id  = editTermRun ( $base, $user, $cwd, (string) ( $body ['command'] ?? '' ), (int) editArg ( $body, 'columns', '120' ) );
      return [ 'id' => $id, 'cwd' => $cwd ];

    case 'read':
      return editTermRead ( $base, $user, editArg ( $body, 'id' ), (int) editArg ( $body, 'offset', '0' ) );

    case 'kill':
      editTermKill ( $base, $user, editArg ( $body, 'id' ), ! empty ( $body ['hard'] ) );
      return [];

    case 'complete':
      return editTermComplete ( editTermCwd ( editArg ( $body, 'cwd' ), editArg ( $body, 'app' ) ), editArg ( $body, 'word' ) );

    case 'where':
      return [ 'cwd' => editTermCwd ( editArg ( $body, 'cwd' ), editArg ( $body, 'app' ) ), 'shell' => editTermShell () ];

  }

  editFail ( 'terminal: run, read, kill, complete or where' );

?>
