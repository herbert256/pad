<?php

  // Which paths editPath lets through and which it refuses, and the applications there are.

  $paths = [];

  foreach ( [ 'index.pad', '_lib/x.php', 'a..b.pad', 'new/dir/file.pad', '', '../demo/index.php',
              'a/../../b', '/etc/passwd', 'a//b', "x\0y", 'a\\b', '.git/config', 'sub/.GIT/x' ] as $rel ) {

    try {
      editPath ( 'demo', 'app', $rel );
      $answer = 'ok';
    } catch ( RuntimeException $e ) {
      $answer = 'refused';
    }

    $paths [] = [ 'rel' => addcslashes ( $rel, "\0..\37\\" ), 'answer' => $answer ];

  }

  $names = [];

  foreach ( [ 'page.pad', '.htaccess', 'a/b', '..', '', '.git' ] as $name ) {
    try {
      editName ( $name );
      $names [] = [ 'name' => $name, 'answer' => 'ok' ];
    } catch ( RuntimeException $e ) {
      $names [] = [ 'name' => $name, 'answer' => 'refused' ];
    }
  }

  $apps   = editApps ();
  $known  = implode ( ' ', array_map ( fn ( $a ) => $a . '=' . ( isset ( $apps [$a] ) ? 'yes' : 'no' ), [ 'demo', 'regression/main', '_common', 'regression', 'nothere' ] ) );
  $roots  = implode ( ' ', array_keys ( editRoots ( 'demo' ) ) ) . ' / ' . implode ( ' ', array_keys ( editRoots ( '_common' ) ) );
  $noApp  = 'ok';

  try {
    editRoot ( '../pad', 'app' );
  } catch ( RuntimeException $e ) {
    $noApp = 'refused';
  }

?>
