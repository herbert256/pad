<?php

  // The terminal's jobs, in a scratch directory of their own: a command's output, both
  // streams, its exit code and the directory it left the shell in; a job that is stopped,
  // with what it started; and Tab's completion of a name.

  $base = editTermBase ( sys_get_temp_dir () . '/pad-edit-test-' . getmypid () . '/' );

  $wait = function ( $id ) use ( $base ) {
    $text = '';
    $offset = 0;
    for ( $try = 0; $try < 200; $try++ ) {
      $read    = editTermRead ( $base, 'anna', $id, $offset );
      $text   .= $read ['text'];
      $offset  = $read ['offset'];
      if ( $read ['done'] )
        return $read + [ 'all' => $text ];
      usleep ( 25000 );
    }
    return [ 'all' => $text . '(not done)', 'exit' => NULL, 'cwd' => '', 'stopped' => FALSE ];
  };

  $ran = $wait ( editTermRun ( $base, 'anna', APPS . 'demo', "echo hello\necho oops >&2\ncd _tags && echo é\nexit 3" ) );

  $runText    = str_replace ( "\n", ' / ', trim ( $ran ['all'] ) );
  $runExit    = $ran ['exit'];
  $runCwd     = basename ( (string) $ran ['cwd'] );

  $long       = editTermRun ( $base, 'anna', APPS . 'demo', "sleep 30 & sleep 30\necho never" );
  usleep ( 200000 );
  $pid        = (int) file_get_contents ( $base . "anna-$long/pid" );
  editTermKill ( $base, 'anna', $long );
  $stopped    = $wait ( $long );
  $stopText   = $stopped ['stopped'] ? 'stopped' : 'not stopped';
  $stopAlive  = editTermAlive ( $pid ) ? 'still running' : 'gone';
  $stopNever  = str_contains ( $stopped ['all'], 'never' ) ? 'it went on' : 'nothing after it';

  $complete   = implode ( ' ', editTermComplete ( APPS . 'demo', '_inc' ) ) . ' / ' . implode ( ' ', editTermComplete ( APPS . 'demo', 'todo.p' ) );

  try {
    editTermRead ( $base, 'anna', '../../etc', 0 );
    $badId = 'read';
  } catch ( RuntimeException $e ) {
    $badId = 'refused';
  }

  editRemoveTree ( rtrim ( $base, '/' ) );

?>
