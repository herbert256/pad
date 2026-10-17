<?php

  // A file that is a symbolic link - editPath lets one through that points inside the root,
  // and the tree lists it as a file - is saved into the file it points to, the link kept.

  $base = DATA . 'edit-test-' . getmypid () . '/';

  editRemoveTree ( $base );

  editWrite ( $base . 'shared.css', "old\n" );

  @chmod ( $base . 'shared.css', 0640 );
  @symlink ( 'shared.css', $base . 'site.css' );

  editWrite ( $base . 'site.css', "new\n" );

  clearstatcache ();

  $link   = is_link ( $base . 'site.css' ) ? 'yes' : 'no';
  $shared = trim ( file_get_contents ( $base . 'shared.css' ) );
  $site   = trim ( file_get_contents ( $base . 'site.css' ) );
  $mode   = decoct ( fileperms ( $base . 'shared.css' ) & 0777 );
  $left   = implode ( ' ', array_values ( array_diff ( scandir ( $base ), [ '.', '..' ] ) ) );

  editRemoveTree ( $base );

?>
