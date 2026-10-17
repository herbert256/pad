<?php

  // The earlier versions of a file are the editor's own, under DATA/edit/history/, and the
  // machine's other users do not read them: a .env closed to them (0600) holds a password,
  // and every save of it through the editor keeps its old text there.

  $base = DATA . 'edit-test-' . getmypid () . '/';

  editRemoveTree ( $base );

  editWrite ( $base . 'work/.env', "padSqlPassword=hunter2\n" );

  @chmod ( $base . 'work/.env', 0600 );

  $id  = editHistoryPut ( $base, 'demo', 'app', '.env', file_get_contents ( $base . 'work/.env' ), 'anna', 50 );
  $dir = editHistoryDir ( $base, 'demo', 'app', '.env' );

  $file   = decoct ( fileperms ( $base . 'work/.env' ) & 0777 );
  $copy   = decoct ( fileperms ( "$dir$id.txt" ) & 0777 );
  $folder = decoct ( fileperms ( $dir ) & 0777 );
  $back   = trim ( editHistoryGet ( $base, 'demo', 'app', '.env', $id ) );

  editRemoveTree ( $base );

?>
