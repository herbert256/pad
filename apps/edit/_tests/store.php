<?php

  // Users, history and trash in a scratch directory under DATA/, removed again at the end.

  $base = DATA . 'edit-test-' . getmypid () . '/';

  editRemoveTree ( $base );

  // users

  $stamp   = editUserAdd ( $base, 'anna', 'first-password' );
  $userOut = [];

  $userOut [] = 'check right: ' . ( editUserCheck ( $base, 'anna', 'first-password' ) === $stamp ? 'yes' : 'no' );
  $userOut [] = 'check wrong: ' . ( editUserCheck ( $base, 'anna', 'nope-nope-nope' ) === '' ? 'refused' : 'let in' );
  $userOut [] = 'check nobody: ' . ( editUserCheck ( $base, 'bert', 'first-password' ) === '' ? 'refused' : 'let in' );
  $userOut [] = 'session valid: ' . ( editUserValid ( $base, 'anna', $stamp ) ? 'yes' : 'no' );

  $newStamp = editUserPasswordSet ( $base, 'anna', 'second-password' );

  $userOut [] = 'old session after a new password: ' . ( editUserValid ( $base, 'anna', $stamp ) ? 'valid' : 'ended' );
  $userOut [] = 'new session: ' . ( editUserValid ( $base, 'anna', $newStamp ) ? 'valid' : 'ended' );
  $userOut [] = 'file mode: ' . decoct ( fileperms ( $base . 'users.json' ) & 0777 );

  foreach ( [ 'a b', 'x', str_repeat ( 'n', 41 ) ] as $bad )
    try {
      editUserAdd ( $base, $bad, 'long-enough-password' );
      $userOut [] = "name '$bad': taken";
    } catch ( RuntimeException $e ) {
      $userOut [] = 'name ' . strlen ( $bad ) . ' chars: ' . ( $bad == 'x' ? 'taken' : 'refused' );
    }

  try {
    editUserAdd ( $base, 'short', 'short' );
    $userOut [] = 'short password: taken';
  } catch ( RuntimeException $e ) {
    $userOut [] = 'short password: refused';
  }

  editUserDelete ( $base, 'x' );

  try {
    editUserDelete ( $base, 'anna' );
    $userOut [] = 'last user: deleted';
  } catch ( RuntimeException $e ) {
    $userOut [] = 'last user: kept';
  }

  // history: three saves, two kept

  foreach ( [ 'one', 'two', 'three' ] as $text ) {
    editHistoryPut ( $base, 'demo', 'app', 'a..b.pad', $text, 'anna', 2 );
    usleep ( 2000 );
  }

  $history     = editHistoryList ( $base, 'demo', 'app', 'a..b.pad' );
  $historyOut  = count ( $history ) . ' kept, newest: ' . editHistoryGet ( $base, 'demo', 'app', 'a..b.pad', $history [0] ['id'] )
               . ', oldest kept: ' . editHistoryGet ( $base, 'demo', 'app', 'a..b.pad', $history [1] ['id'] );

  // trash: a file goes in and comes back

  editWrite ( $base . 'work/page.pad', 'content' );

  $id       = editTrashPut ( $base, 'demo', 'app', 'page.pad', $base . 'work/page.pad', 'anna' );
  $trashOut = [];

  $trashOut [] = 'in the trash: ' . count ( editTrashList ( $base ) ) . ', file gone: ' . ( file_exists ( $base . 'work/page.pad' ) ? 'no' : 'yes' );

  editTrashRestore ( $base, $id, $base . 'work/page.pad' );

  $trashOut [] = 'put back: ' . file_get_contents ( $base . 'work/page.pad' ) . ', trash: ' . count ( editTrashList ( $base ) );

  $id2 = editTrashPut ( $base, 'demo', 'app', 'page.pad', $base . 'work/page.pad', 'anna' );

  $trashOut [] = 'purged after 30 days: ' . editTrashPurge ( $base, 30 ) . ', after -1 days: ' . editTrashPurge ( $base, -1 );

  editRemoveTree ( $base );

  $cleaned = is_dir ( $base ) ? 'no' : 'yes';

?>
