<?php

  // A copy - the tree's Duplicate, the copy call - keeps the mode of what it copies, as a
  // save keeps a file's: a .env closed to the machine's other users (0600) holds a password,
  // and its copy was 0644 under the server's umask, readable by all of them; a directory
  // closed to them, with such a file in it, was copied as 0755.

  $base = DATA . 'edit-test-' . getmypid () . '/';

  editRemoveTree ( $base );

  editWrite ( $base . 'work/.env', "padSqlPassword=hunter2\n" );
  editWrite ( $base . 'work/secret/.env', "padSqlPassword=hunter2\n" );
  editWrite ( $base . 'work/open.txt', "open\n" );

  @chmod ( $base . 'work/.env', 0600 );
  @chmod ( $base . 'work/secret/.env', 0600 );
  @chmod ( $base . 'work/secret', 0700 );
  @chmod ( $base . 'work/open.txt', 0644 );

  editCopyTree ( $base . 'work/.env', $base . 'work/.env.copy' );
  editCopyTree ( $base . 'work/open.txt', $base . 'work/open.copy.txt' );
  editCopyTree ( $base . 'work/secret', $base . 'work/secret.copy' );

  $mode = fn ( $path ) => decoct ( fileperms ( $base . $path ) & 0777 );

  $shown = implode ( ' ', [ $mode ( 'work/.env.copy' ), $mode ( 'work/open.copy.txt' ), $mode ( 'work/secret.copy' ), $mode ( 'work/secret.copy/.env' ) ] );

  editRemoveTree ( $base );

?>
