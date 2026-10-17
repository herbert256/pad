<?php

  // A new card whose details are too long comes back with a banner true for it - the form
  // has two rules, the title's and the details' - where it said "The card needs a title of
  // at most 100 characters." above the title Fine. The board's page is rendered as the post
  // would be, in a child process of its own with a session holding the post's token; a
  // refused post stores nothing.

  $render   = dirname ( PAD ) . '/editors/render.php';
  $sessions = DATA . 'kanban-test-' . getmypid ();
  $child    = 'session_save_path ( $argv [3] ); session_start (); $_SESSION ["padCsrf"] = str_repeat ( "a", 64 ); '
            . '$_SERVER ["REQUEST_METHOD"] = "POST"; parse_str ( $argv [1], $_POST ); $_POST ["padCsrfToken"] = $_SESSION ["padCsrf"]; $_REQUEST = $_POST; '
            . '$render = $argv [2]; $argv = [ "render.php", "kanban", "index" ]; include $render;';

  @mkdir ( $sessions, 0700, TRUE );

  $banner = [];

  foreach ( [ 'title=Fine&body=' . str_repeat ( 'x', 2001 ), 'title=' . str_repeat ( 'x', 101 ) . '&body=Fine' ] as $post ) {

    $process = proc_open ( [ PHP_BINARY, '-r', $child, "padForm=card&label=&$post", $render, $sessions ], [ 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ], $pipes );
    $out     = stream_get_contents ( $pipes [1] );

    stream_get_contents ( $pipes [2] );
    proc_close ( $process );

    $banner [] = preg_match ( '/<div class="error" role="alert">(.*?)<\/div>/s', $out, $m ) ? trim ( $m [1] ) : 'no banner';

  }

  array_map ( 'unlink', glob ( "$sessions/*" ) ?: [] );
  @rmdir ( $sessions );

  $shown = implode ( "\n", $banner );

?>
