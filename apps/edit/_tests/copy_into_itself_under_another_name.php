<?php

  // A directory is not copied into itself under another spelling of its path: parts/old is
  // inside Parts on a volume that ignores case (macOS), and link/old is inside it when link
  // is a symbolic link to Parts. The paths as text did not say so: the copy walked into what
  // it had just made, 242 levels of Parts/old/old/... until the path was too long, then
  // failed and left them. What Parts holds afterwards tells, on any volume - where case
  // counts, parts/old is a copy beside Parts.

  $base = DATA . 'edit-test-' . getmypid () . '/';

  editRemoveTree ( $base );

  editWrite ( $base . 'Parts/a.pad', 'a' );

  @symlink ( 'Parts', $base . 'link' );

  $out = [];

  foreach ( [ 'Parts/old', 'parts/old', 'link/old' ] as $to ) {

    $said = 'copied';

    try {
      editCopy ( $base . 'Parts', $base . $to );
    } catch ( RuntimeException $e ) {
      $said = $e->getMessage ();
    }

    clearstatcache ();

    $out [] = "Parts to $to: Parts holds " . implode ( ' ', array_values ( array_diff ( scandir ( $base . 'Parts' ), [ '.', '..' ] ) ) )
            . ( $to == 'link/old' ? ", $said" : '' );

    editRemoveTree ( $base . 'Parts/old' );

  }

  editRemoveTree ( $base );

?>
