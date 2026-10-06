<?php

  // The framework as a root of its own: the same for every application and none needed,
  // its files listed and found, a path out of it refused; and the save guard - a PHP file
  // the editor itself runs on is not saved when it does not parse, any other is.

  $root    = editRoot ( '', 'pad' ) === realpath ( editHome () . '/pad' ) . '/' ? 'pad/' : 'elsewhere';
  $sameFor = editRoot ( 'demo', 'pad' ) === editRoot ( '', 'pad' ) ? 'yes' : 'no';
  $noApp   = editRootApp ( 'nonsense', 'pad' ) === '' ? 'none needed' : 'needed';

  $tree    = editEngineTree ();
  $listed  = in_array ( 'lib/api.php', array_column ( $tree, 'path' ) ) ? 'yes' : 'no';
  $roots   = implode ( ' ', array_unique ( array_column ( $tree, 'root' ) ) );
  $inApp   = in_array ( 'pad', array_column ( editTree ( 'demo' ), 'root' ) ) ? 'yes' : 'no';

  $paths = [];

  foreach ( [ 'lib/api.php', '../apps/edit/_config/config.php', 'lib/../../www/pad.php' ] as $rel ) {
    try {
      editPath ( '', 'pad', $rel );
      $paths [] = "$rel: ok";
    } catch ( RuntimeException $e ) {
      $paths [] = "$rel: refused";
    }
  }

  $runsOn = implode ( ' ', array_map ( fn ( $f ) => $f [0] . '/' . $f [1] . '/' . $f [2] . '=' . ( editRunsOn ( ...$f ) ? 'guarded' : 'free' ),
                        [ [ '', 'pad', 'lib/api.php' ], [ 'edit', 'app', '_lib/paths.php' ], [ 'edit', 'www', 'index.php' ],
                          [ 'demo', 'app', 'todo.php' ], [ '', 'pad', 'README.md' ] ] ) );

  try {
    editToRoot ( 'pad', 'app' );
    $across = 'allowed';
  } catch ( RuntimeException $e ) {
    $across = 'refused';
  }

?>
