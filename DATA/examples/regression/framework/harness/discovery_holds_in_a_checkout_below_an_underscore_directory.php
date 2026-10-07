<?php

  // The page walk leaves out what stands in an _xxx directory of an application - not what
  // the checkout itself stands in. Reached through /tmp/_pad<pid>/pad, a symlink to this
  // checkout - a CI runner's _work directory is such a path - the walk found no page at
  // all: every path held a /_, and the empty list then ended on ksort(NULL), so the covering
  // suites and develop's harvest had nothing to fetch. The same discovery case is rendered
  // there on the command line; the link is taken away again.

  $underDir  = sys_get_temp_dir () . '/_pad' . getmypid ();
  $underLink = "$underDir/pad";

  if ( ! is_dir ( $underDir ) )
    mkdir ( $underDir, 0700, TRUE );

  if ( ! is_link ( $underLink ) )
    symlink ( dirname ( APPS ), $underLink );

  $underPhp = ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';

  $underOut = (string) shell_exec ( 'PAD_HOME=' . escapeshellarg ( $underLink ) . ' ' . escapeshellarg ( $underPhp ) . ' '
                                    . escapeshellarg ( "$underLink/apps/cli/pad" )
                                    . ' render regression/framework harness/discovery_lists_an_ordinary_page 2>&1' );

  unlink ( $underLink );
  rmdir  ( $underDir );

  $underAnswer = str_starts_with ( trim ( $underOut ), '{' ) ? 'error: ' . ( json_decode ( $underOut, TRUE ) ['error'] ?? '' ) : trim ( $underOut );

?>
