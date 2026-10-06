<?php

  // The editor's terminal: a command line whose commands run on this machine, in a shell,
  // as the web server's user - what the editor can do anyway, since it writes PHP files.
  //
  // A command is a job: a script for the shell - the command after a cd to the terminal's
  // directory, with an EXIT trap that writes down the exit code and the directory the
  // command left the shell in, so a cd carries over to the next command - started in the
  // background in a process group of its own (set -m), its output going to a file. The
  // request that starts it returns at once; the browser then reads the file from where it
  // stopped, a few times a second, until the job is done. Stopping it signals the whole
  // group, so what the command started stops too. There is no terminal device: a command
  // gets no input, and full-screen programs (vi, top) do not work.
  //
  // The jobs live in the system's temporary directory, mode 0700, not under DATA/: their
  // output can hold anything, and DATA/ is browsable on a development server. A job whose
  // browser has not asked for its output for two minutes - the tab was closed - is stopped,
  // and finished jobs go after an hour.
  //
  // editTermBase      the directory of the jobs
  // editTermRun       starts a command: its job id
  // editTermRead      the output since an offset, and whether the job is done
  // editTermKill      stops a job: TERM, or KILL when $hard
  // editTermComplete  the names in a directory that start with what was typed - Tab
  // editTermSweep     stops abandoned jobs, removes old ones

  function editTermBase ( $base = NULL ) {

    $base = $base ?? sys_get_temp_dir () . '/pad-edit-' . substr ( md5 ( editHome () ), 0, 12 ) . '/';

    if ( ! is_dir ( $base ) )
      @mkdir ( $base, 0700, TRUE );

    // The name is known to anyone who knows where the checkout is, in a directory every
    // user of the machine may write in: one that another user made there first - or a link
    // they put there - held the scripts the terminal runs, the output of its commands and
    // the debugger's token, theirs to read and to swap. It is this user's own and closed to
    // everyone else, or nothing runs.

    $dir = rtrim ( $base, '/' );

    clearstatcache ( TRUE, $dir );

    if ( is_link ( $dir ) or ! is_dir ( $dir ) or ( fileperms ( $dir ) & 0077 )
         or ( function_exists ( 'posix_geteuid' ) and fileowner ( $dir ) !== posix_geteuid () ) )
      editFail ( "the directory of the terminal's jobs, $dir, is not this user's own and closed to others" );

    return $base;

  }

  function editTermShell () {

    global $editShell;

    foreach ( [ (string) ( $editShell ?? '' ), '/bin/bash', '/bin/zsh', '/bin/sh' ] as $shell )
      if ( $shell !== '' and is_executable ( $shell ) )
        return $shell;

    editFail ( 'there is no shell to run commands with' );

  }

  // The environment of a command: the web server's own, with a PATH that finds this PHP,
  // Homebrew's tools and the pad command, a HOME, and the settings that make tools write
  // colour although no terminal reads it.

  function editTermEnv ( $columns ) {

    $env  = getenv ();
    $home = function_exists ( 'posix_getpwuid' ) ? ( posix_getpwuid ( posix_geteuid () ) ['dir'] ?? '' ) : '';
    $path = array_merge ( [ editHome () . '/apps/cli', PHP_BINDIR, '/opt/homebrew/bin', '/opt/homebrew/sbin', '/usr/local/bin' ],
                          explode ( ':', $env ['PATH'] ?? '/usr/bin:/bin:/usr/sbin:/sbin' ) );

    return array_merge ( $env, [
      'PATH'                => implode ( ':', array_unique ( array_filter ( $path ) ) ),
      'HOME'                => $env ['HOME'] ?? ( $home ?: editHome () ),
      'PAD_HOME'            => editHome (),
      'TERM'                => 'xterm-256color',
      'CLICOLOR'            => '1',
      'CLICOLOR_FORCE'      => '1',
      'FORCE_COLOR'         => '1',
      'COLUMNS'             => (string) max ( 20, min ( 400, (int) $columns ) ),
      'LINES'               => '40',
      'PAGER'               => 'cat',
      'GIT_PAGER'           => 'cat',
      'GIT_TERMINAL_PROMPT' => '0',
      'LANG'                => $env ['LANG'] ?? 'en_US.UTF-8'
    ] );

  }

  function editTermJob ( $base, $user, $id ) {

    if ( ! preg_match ( '/^[0-9a-f]{16}$/', (string) $id ) )
      editFail ( 'there is no such job' );

    $dir = $base . editUserName ( $user ) . "-$id/";

    if ( ! is_dir ( $dir ) )
      editFail ( 'that job is gone' );

    return $dir;

  }

  function editTermRun ( $base, $user, $cwd, $command, $columns = 120 ) {

    editTermSweep ( $base );

    if ( trim ( $command ) === '' )
      editFail ( 'there is no command' );

    $id  = bin2hex ( random_bytes ( 8 ) );
    $dir = $base . editUserName ( $user ) . "-$id/";

    if ( ! @mkdir ( $dir, 0700, TRUE ) )
      editFail ( 'the job directory could not be made' );

    $script = 'trap \'code=$?; pwd > ' . escapeshellarg ( $dir . 'cwd' ) . '; echo $code > ' . escapeshellarg ( $dir . 'exit' ) . "' EXIT\n"
            . 'cd ' . escapeshellarg ( $cwd ) . " || exit 1\n"
            . $command . "\n";

    file_put_contents ( $dir . 'run.sh', $script );
    file_put_contents ( $dir . 'command', $command );
    touch ( $dir . 'seen' );

    $launch = 'set -m; ' . escapeshellarg ( editTermShell () ) . ' ' . escapeshellarg ( $dir . 'run.sh' )
            . ' > ' . escapeshellarg ( $dir . 'out' ) . ' 2>&1 < /dev/null & echo $!';

    $proc = proc_open ( [ '/bin/sh', '-c', $launch ], [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'file', '/dev/null', 'w' ] ],
                        $pipes, $cwd, editTermEnv ( $columns ) );

    if ( ! is_resource ( $proc ) )
      editFail ( 'the command could not be started' );

    $pid = (int) trim ( (string) stream_get_contents ( $pipes [1] ) );

    fclose ( $pipes [1] );
    proc_close ( $proc );

    if ( $pid <= 0 )
      editFail ( 'the command could not be started' );

    file_put_contents ( $dir . 'pid', $pid );

    return $id;

  }

  function editTermAlive ( $pid ) {

    if ( $pid <= 0 )
      return FALSE;

    if ( function_exists ( 'posix_kill' ) )
      return posix_kill ( $pid, 0 );

    exec ( 'kill -0 ' . (int) $pid . ' 2>/dev/null', $out, $code );

    return $code === 0;

  }

  // At most $max bytes from $offset on, ending on a whole UTF-8 character: a character cut
  // in two waits for the next read. Done is the job's end - its exit code written, or its
  // process gone - with every byte of its output read.

  function editTermRead ( $base, $user, $id, $offset, $max = 262144 ) {

    $dir    = editTermJob ( $base, $user, $id );
    $file   = $dir . 'out';
    $offset = max ( 0, (int) $offset );

    touch ( $dir . 'seen' );
    clearstatcache ( TRUE, $file );

    $size   = is_file ( $file ) ? filesize ( $file ) : 0;
    $text   = '';
    $ended  = ( is_file ( $dir . 'exit' ) || ! editTermAlive ( (int) @file_get_contents ( $dir . 'pid' ) ) );

    // A command that writes without end - yes, a loop - is stopped before it fills the disk.

    if ( $size > 64 * 1024 * 1024 and ! $ended ) {
      editTermKill ( $base, $user, $id, TRUE );
      $ended = TRUE;
    }

    if ( $size > $offset ) {

      $chunk = (string) file_get_contents ( $file, FALSE, NULL, $offset, $max );
      $text  = $chunk;

      for ( $cut = 0; $cut < 4 and $text !== '' and ! mb_check_encoding ( $text, 'UTF-8' ); $cut++ )
        $text = substr ( $text, 0, -1 );

      if ( ! mb_check_encoding ( $text, 'UTF-8' ) or ( $text === '' and $ended ) ) {
        $text = mb_scrub ( $chunk, 'UTF-8' );
        $offset += strlen ( $chunk );
      } else
        $offset += strlen ( $text );

    }

    $done = ( $ended && $offset >= $size );
    $exit = is_file ( $dir . 'exit' ) ? (int) trim ( (string) file_get_contents ( $dir . 'exit' ) ) : NULL;

    return [ 'text'    => $text,
             'offset'  => $offset,
             'done'    => $done,
             'exit'    => $done ? $exit : NULL,
             'stopped' => ( $done && is_file ( $dir . 'stopped' ) ),
             'cwd'     => ( $done && is_file ( $dir . 'cwd' ) ) ? trim ( (string) file_get_contents ( $dir . 'cwd' ) ) : NULL ];

  }

  function editTermKill ( $base, $user, $id, $hard = FALSE ) {

    $dir = editTermJob ( $base, $user, $id );
    $pid = (int) @file_get_contents ( $dir . 'pid' );

    touch ( $dir . 'stopped' );

    if ( $pid <= 0 )
      return;

    $signal = $hard ? 9 : 15;

    if ( function_exists ( 'posix_kill' ) )
      posix_kill ( - $pid, $signal ) or posix_kill ( $pid, $signal );
    else
      exec ( "kill -$signal -- -$pid 2>/dev/null || kill -$signal $pid 2>/dev/null" );

    // A moment for it to go - and for its EXIT trap to write the directory down - so the
    // read that follows the stop finds it done.

    for ( $wait = 0; $wait < 50 and editTermAlive ( $pid ); $wait++ )
      usleep ( 10000 );

  }

  function editTermSweep ( $base ) {

    foreach ( (array) @scandir ( $base ) as $name ) {

      if ( ! preg_match ( '/^[A-Za-z0-9_-]+-[0-9a-f]{16}$/', (string) $name ) )
        continue;

      $dir  = "$base$name/";
      $pid  = (int) @file_get_contents ( $dir . 'pid' );
      $seen = (int) @filemtime ( $dir . 'seen' );

      if ( editTermAlive ( $pid ) ) {
        if ( $seen < time () - 120 and function_exists ( 'posix_kill' ) ) {
          touch ( $dir . 'stopped' );
          posix_kill ( - $pid, 9 );
        }
        continue;
      }

      if ( $seen < time () - 3600 )
        editRemoveTree ( rtrim ( $dir, '/' ) );

    }

  }

  // What Tab completes: the names in the directory of the word typed, starting with its
  // last part; a directory with a / after it.

  function editTermComplete ( $cwd, $word ) {

    $word = (string) $word;
    $dir  = str_contains ( $word, '/' ) ? substr ( $word, 0, strrpos ( $word, '/' ) + 1 ) : '';
    $part = substr ( $word, strlen ( $dir ) );
    $look = ( $dir !== '' and $dir [0] == '/' ) ? $dir : rtrim ( $cwd, '/' ) . '/' . $dir;

    if ( str_starts_with ( $dir, '~/' ) and getenv ( 'HOME' ) )
      $look = getenv ( 'HOME' ) . substr ( $dir, 1 );

    $names = [];

    foreach ( (array) @scandir ( $look ) as $name ) {
      $name = (string) $name;
      if ( $name === '.' or $name === '..' or $name === '' or ! str_starts_with ( $name, $part ) )
        continue;
      if ( $name [0] == '.' and ( $part === '' or $part [0] != '.' ) )
        continue;
      $names [] = $dir . $name . ( is_dir ( $look . $name ) ? '/' : '' );
    }

    sort ( $names, SORT_STRING | SORT_FLAG_CASE );

    return array_slice ( $names, 0, 200 );

  }

  // The directory a command starts in: the one the terminal is in, when it still exists;
  // else the root of the application being edited; else the checkout.

  function editTermCwd ( $cwd, $app ) {

    if ( $cwd !== '' and is_dir ( $cwd ) )
      return (string) realpath ( $cwd );

    if ( $app !== '' and isset ( editApps () [$app] ) )
      return rtrim ( editRoot ( $app, 'app' ), '/' );

    return editHome ();

  }

?>
