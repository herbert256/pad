<?php

  // What git says about the files of an application, when the checkout is a git repository
  // and a git binary is found: which files changed (the markers in the tree), the version of
  // a file in HEAD (compare with HEAD), and the branch. The editor never commits, stages or
  // changes anything in git. Every call is an argument list, never a shell.

  function editGit () {

    static $git = NULL;

    if ( $git !== NULL )
      return $git;

    $git = '';

    if ( ! is_dir ( editHome () . '/.git' ) )
      return $git;

    foreach ( [ '/opt/homebrew/bin/git', '/usr/local/bin/git', '/usr/bin/git', '/bin/git' ] as $one )
      if ( is_executable ( $one ) )
        return $git = $one;

    return $git;

  }

  function editGitRun ( $args, $timeout = 10 ) {

    if ( ! editGit () )
      return NULL;

    [ $code, $out ] = editRun ( array_merge ( [ editGit (), '-C', editHome () ], $args ), NULL, $timeout,
                                [ 'GIT_TERMINAL_PROMPT' => '0', 'GIT_OPTIONAL_LOCKS' => '0' ] );

    return $code === 0 ? $out : NULL;

  }

  // The checkout-relative directory of a root: apps/<app> or www/<app>.

  function editGitDir ( $app, $root ) {

    return ( $root == 'www' ? 'www/' : 'apps/' ) . $app;

  }

  // [ root => [ path => 'M' | 'A' | '?' | 'D' | 'R' ] ] for the files git sees as changed.

  function editGitStatus ( $app ) {

    $roots = editRoots ( $app );
    $out   = editGitRun ( array_merge ( [ 'status', '--porcelain=v1', '-z', '--untracked-files=all', '--' ],
                                        array_map ( fn ( $r ) => editGitDir ( $app, $r ) . '/', array_keys ( $roots ) ) ) );

    if ( $out === NULL )
      return NULL;

    $status  = array_fill_keys ( array_keys ( $roots ), [] );
    $entries = explode ( "\0", $out );

    for ( $i = 0; $i < count ( $entries ); $i++ ) {

      $entry = $entries [$i];

      if ( strlen ( $entry ) < 4 )
        continue;

      $code = substr ( $entry, 0, 2 );
      $path = substr ( $entry, 3 );

      if ( $code [0] == 'R' or $code [0] == 'C' )
        $i++;

      $mark = match ( TRUE ) {
        $code == '??'                                   => '?',
        str_contains ( $code, 'D' )                     => 'D',
        str_contains ( $code, 'A' )                     => 'A',
        str_contains ( $code, 'R' )                     => 'R',
        default                                         => 'M'
      };

      foreach ( array_keys ( $roots ) as $root ) {
        $dir = editGitDir ( $app, $root ) . '/';
        if ( str_starts_with ( $path, $dir ) )
          $status [$root] [ substr ( $path, strlen ( $dir ) ) ] = $mark;
      }

    }

    return $status;

  }

  function editGitBranch () {

    $out = editGitRun ( [ 'rev-parse', '--abbrev-ref', 'HEAD' ] );

    return $out === NULL ? '' : trim ( $out );

  }

  // The text of a file in HEAD; NULL when HEAD does not have it.

  function editGitHead ( $app, $root, $path ) {

    return editGitRun ( [ 'show', 'HEAD:' . editGitDir ( $app, $root ) . '/' . $path ] );

  }

?>
