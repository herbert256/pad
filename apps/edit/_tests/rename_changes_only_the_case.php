<?php

  // A rename that changes only the case of a name - readme.md to README.md - is a rename, on
  // a volume that ignores case (macOS) too: the new name "existed already" there, since it
  // is the file itself. Another file of that name is still not overwritten.

  $base = DATA . 'edit-test-' . getmypid () . '/';

  editRemoveTree ( $base );

  editWrite ( $base . 'readme.md', 'read me' );
  editWrite ( $base . 'other.md', 'other' );
  editCreate ( $base . 'Parts', '', TRUE );

  $out = [];

  foreach ( [ [ 'readme.md', 'README.md' ], [ 'Parts', 'parts' ], [ 'other.md', 'README.md' ] ] as [ $from, $to ] )
    try {
      editMove ( $base . $from, $base . $to );
      $out [] = "$from to $to: moved";
    } catch ( RuntimeException $e ) {
      $out [] = "$from to $to: " . $e->getMessage ();
    }

  $names = array_values ( array_diff ( scandir ( $base ), [ '.', '..' ] ) );
  $out [] = 'names: ' . implode ( ' ', $names );
  $out [] = 'README.md holds: ' . file_get_contents ( $base . 'README.md' );

  editRemoveTree ( $base );

?>
