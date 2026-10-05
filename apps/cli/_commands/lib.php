<?php

  // What the commands of the pad tool share: where things are, how an application and a
  // page are named, which pages an application has, and how to run this script again as a
  // child process.
  //
  // cliHome      the repository root, from PAD_HOME (apps/cli/pad sets it)
  // cliApp       an application name checked against apps/: letters, digits, _ and -,
  //              nested with / like regression/pages
  // cliPages     every page of an application (or of one directory of it), the way the
  //              suites walk them: .pad, .html and .php files outside the _ directories,
  //              less the action-only fixtures that redirect, restart or write, the
  //              bracketed routes, and the directories that are applications of their own
  // cliRun       this script with other arguments, in a child process: [ exit code, output ]
  // cliRunPages  pad render over a list of pages, four child processes at a time
  // cliPhp       the php binary to run it with - the one running now, when that is php

  function cliHome () {

    return rtrim ( getenv ( 'PAD_HOME' ), '/' );

  }

  function cliOut ( $text ) {

    echo $text . "\n";

  }

  function cliFail ( $text ) {

    fwrite ( STDERR, "pad: $text\n" );

    return 1;

  }

  function cliName ( $name ) {

    return (bool) preg_match ( '#^[A-Za-z0-9][A-Za-z0-9_-]*(/[A-Za-z0-9][A-Za-z0-9_-]*)*$#D', $name );

  }

  function cliApp ( $app ) {

    return cliName ( $app ) and is_dir ( cliHome () . "/apps/$app" ) and ! str_contains ( $app, '/_' );

  }

  function cliPages ( $app, $sub = '' ) {

    $dir = cliHome () . "/apps/$app/" . ( $sub === '' ? '' : rtrim ( $sub, '/' ) . '/' );

    return cliPagesWalk ( $dir, $sub === '' ? '' : rtrim ( $sub, '/' ) . '/', $app );

  }

  // A directory with an entry point of its own in www/ is another application - the
  // regression/ tree is one directory of many - and its pages are not this one's.

  function cliPagesWalk ( $dir, $prefix, $app ) {

    $names = $dirs = [];

    if ( ! is_dir ( $dir ) )
      return [];

    foreach ( scandir ( $dir ) as $file ) {

      if ( str_starts_with ( $file, '_' ) or str_starts_with ( $file, '.' ) )
        continue;

      // A bracketed route - products/[id].pad, blog/[year]/ - is reached through a path
      // that fills in its value (lib/route.php), never by its own name: rendered by name it
      // is a page not found, which pad lint counted as a failure and pad export as a page
      // it had to skip.

      if ( str_contains ( $file, '[' ) )
        continue;

      if ( is_dir ( "$dir$file" ) )
        $dirs [] = $file;
      elseif ( preg_match ( '/^(.+)\.(pad|php|html)$/', $file, $m ) )
        $names [ $m [1] ] = TRUE;

    }

    foreach ( array_keys ( $names ) as $name )
      if ( ! file_exists ( "$dir$name.pad" ) and ! file_exists ( "$dir$name.html" ) )
        if ( preg_match ( '/padRedirect|padRestart|padFilePut|padDeleteDataDir/', (string) file_get_contents ( "$dir$name.php" ) ) )
          unset ( $names [$name] );

    $list = [];

    foreach ( array_keys ( $names ) as $name )
      $list [] = $prefix . $name;

    foreach ( $dirs as $one )
      if ( ! file_exists ( cliHome () . "/www/$app/$prefix$one/index.php" ) )
        $list = array_merge ( $list, cliPagesWalk ( "$dir$one/", "$prefix$one/", $app ) );

    sort ( $list );

    return $list;

  }

  function cliPhp () {

    if ( PHP_BINARY !== '' and str_starts_with ( basename ( PHP_BINARY ), 'php' ) and PHP_SAPI === 'cli' )
      return PHP_BINARY;

    return is_executable ( PHP_BINDIR . '/php' ) ? PHP_BINDIR . '/php' : 'php';

  }

  // The script running now - not the one under PAD_HOME, which a scratch home has none of.

  function cliScript () {

    return dirname ( __DIR__ ) . '/pad';

  }

  // pad render for every page of $pages, each in a child process of its own with the
  // environment plus $env, four at a time - an error ends the process it happens in, and
  // a page is not to stop the others. Returns [ page => [ exit code, stdout, stderr ] ],
  // by page.

  function cliRunPages ( $app, $pages, $env ) {

    $queue   = $pages;
    $running = [];
    $result  = [];

    while ( $queue or $running ) {

      while ( $queue and count ( $running ) < 4 ) {

        $page = array_shift ( $queue );

        $proc = proc_open ( [ cliPhp (), cliScript (), 'render', $app, $page ],
                            [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ],
                            $pipes, NULL, array_merge ( getenv (), $env ) );

        stream_set_blocking ( $pipes [1], FALSE );
        stream_set_blocking ( $pipes [2], FALSE );

        $running [$page] = [ $proc, $pipes, '', '' ];

      }

      foreach ( $running as $page => $one ) {

        $running [$page] [2] .= stream_get_contents ( $one [1] [1] );
        $running [$page] [3] .= stream_get_contents ( $one [1] [2] );

        $status = proc_get_status ( $one [0] );

        if ( $status ['running'] )
          continue;

        $running [$page] [2] .= stream_get_contents ( $one [1] [1] );
        $running [$page] [3] .= stream_get_contents ( $one [1] [2] );

        fclose ( $one [1] [1] );
        fclose ( $one [1] [2] );
        proc_close ( $one [0] );

        $result [$page] = [ $status ['exitcode'], $running [$page] [2], $running [$page] [3] ];

        unset ( $running [$page] );

      }

      if ( $running )
        usleep ( 5000 );

    }

    ksort ( $result );

    return $result;

  }

  // Runs pad again with $args, the environment plus $env, and returns its exit code and
  // what it wrote to stdout.

  function cliRun ( $args, $env = [] ) {

    $proc = proc_open ( array_merge ( [ cliPhp (), cliScript () ], $args ),
                        [ 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ],
                        $pipes, NULL, array_merge ( getenv (), $env ) );

    if ( ! is_resource ( $proc ) )
      return [ 1, '' ];

    $out = stream_get_contents ( $pipes [1] );
    stream_get_contents ( $pipes [2] );

    fclose ( $pipes [1] );
    fclose ( $pipes [2] );

    return [ proc_close ( $proc ), $out ];

  }

?>
