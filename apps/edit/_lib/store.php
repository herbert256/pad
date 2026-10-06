<?php

  // What the editor keeps of its own, under DATA/edit/: the users who may log in, the
  // earlier versions of every file saved here, and the trash. Every function takes the base
  // directory, DATA/edit/ for the application (editStore), a scratch directory under DATA/
  // for the tests.
  //
  // A file's history and a trashed item live in directories named after a hash - of
  // app|root|path for the history - with a meta.json saying what they are: a file name may
  // hold anything a path may, a..b.pad too, which padFileCheck would refuse as a path
  // under DATA.
  //
  // The files are written with editWrite, like an application's own, not padFilePut: a
  // failed write is the call's error to report, where padFilePut raises a PAD error that
  // ends the request with an HTML page.

  function editStore () {

    return DATA . 'edit/';

  }

  function editJsonRead ( $file, $default = [] ) {

    if ( ! is_file ( $file ) )
      return $default;

    $data = json_decode ( (string) file_get_contents ( $file ), TRUE );

    return is_array ( $data ) ? $data : $default;

  }

  function editJsonWrite ( $file, $data ) {

    editWrite ( $file, json_encode ( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" );

  }

  // ---------------------------------------------------------------------------------------
  // Users: a name and a password hash, and a stamp that changes with the password. A session
  // carries the stamp it logged in with, so a new password - or a deleted user - ends every
  // session that still has the old one (_guard.php).
  // ---------------------------------------------------------------------------------------

  function editUsers ( $base ) {

    return editJsonRead ( $base . 'users.json' ) ['users'] ?? [];

  }

  function editUsersSave ( $base, $users ) {

    ksort ( $users, SORT_STRING | SORT_FLAG_CASE );

    editJsonWrite ( $base . 'users.json', [ 'users' => (object) $users ] );

    @chmod ( $base . 'users.json', 0600 );

  }

  function editUserName ( $name ) {

    $name = trim ( (string) $name );

    if ( ! preg_match ( '/^[A-Za-z0-9_-]{1,40}$/', $name ) )
      editFail ( 'a user name is 1 to 40 letters, digits, _ or -' );

    return $name;

  }

  function editUserPassword ( $password ) {

    if ( ! is_string ( $password ) or strlen ( $password ) < 8 )
      editFail ( 'a password has at least 8 characters' );

    if ( strlen ( $password ) > 200 )
      editFail ( 'a password has at most 200 characters' );

    return $password;

  }

  function editUserAdd ( $base, $name, $password ) {

    $name     = editUserName ( $name );
    $password = editUserPassword ( $password );
    $users    = editUsers ( $base );

    if ( isset ( $users [$name] ) )
      editFail ( "there is a user named '$name' already" );

    $users [$name] = [ 'hash' => padHash ( $password ), 'stamp' => bin2hex ( random_bytes ( 8 ) ), 'created' => date ( 'c' ) ];

    editUsersSave ( $base, $users );

    return $users [$name] ['stamp'];

  }

  function editUserPasswordSet ( $base, $name, $password ) {

    $password = editUserPassword ( $password );
    $users    = editUsers ( $base );

    if ( ! isset ( $users [$name] ) )
      editFail ( "there is no user named '" . padMakeSafe ( $name, 40 ) . "'" );

    $users [$name] ['hash']  = padHash ( $password );
    $users [$name] ['stamp'] = bin2hex ( random_bytes ( 8 ) );

    editUsersSave ( $base, $users );

    return $users [$name] ['stamp'];

  }

  function editUserDelete ( $base, $name ) {

    $users = editUsers ( $base );

    if ( ! isset ( $users [$name] ) )
      editFail ( "there is no user named '" . padMakeSafe ( $name, 40 ) . "'" );

    if ( count ( $users ) == 1 )
      editFail ( 'the last user stays: nobody could log in anymore' );

    unset ( $users [$name] );

    editUsersSave ( $base, $users );

  }

  // The stamp of a user whose password matches, '' otherwise. A name that is no user still
  // costs a hash check, so the answer takes as long either way.

  function editUserCheck ( $base, $name, $password ) {

    $users = editUsers ( $base );
    $user  = $users [ (string) $name ] ?? NULL;

    static $none = NULL;

    $hash = $user ['hash'] ?? ( $none ??= padHash ( bin2hex ( random_bytes ( 8 ) ) ) );

    if ( ! padHashCheck ( (string) $password, $hash ) or ! $user )
      return '';

    return (string) $user ['stamp'];

  }

  function editUserValid ( $base, $name, $stamp ) {

    $user = editUsers ( $base ) [ (string) $name ] ?? NULL;

    return $user and is_string ( $stamp ) and $stamp !== '' and hash_equals ( (string) $user ['stamp'], $stamp );

  }

  // ---------------------------------------------------------------------------------------
  // History: before a save overwrites a file, its old text goes into the file's history
  // directory as <microtime>-<user>.txt; the oldest go when there are more than $keep.
  // ---------------------------------------------------------------------------------------

  function editHistoryDir ( $base, $app, $root, $path ) {

    return $base . 'history/' . sha1 ( "$app|$root|$path" ) . '/';

  }

  function editHistoryPut ( $base, $app, $root, $path, $text, $user, $keep ) {

    $dir = editHistoryDir ( $base, $app, $root, $path );

    if ( ! is_file ( $dir . 'meta.json' ) )
      editJsonWrite ( $dir . 'meta.json', [ 'app' => $app, 'root' => $root, 'path' => $path ] );

    $id = sprintf ( '%.6f', microtime ( TRUE ) );
    $id = str_replace ( '.', '', $id ) . '-' . editUserName ( $user ?: 'cli' );

    editWrite ( "$dir$id.txt", $text );

    $list = editHistoryList ( $base, $app, $root, $path );

    foreach ( array_slice ( $list, max ( 1, (int) $keep ) ) as $old )
      @unlink ( $dir . $old ['id'] . '.txt' );

    return $id;

  }

  // Newest first: id, time (seconds), user and size of each version.

  function editHistoryList ( $base, $app, $root, $path ) {

    $dir  = editHistoryDir ( $base, $app, $root, $path );
    $list = [];

    foreach ( (array) @scandir ( $dir ) as $name )
      if ( preg_match ( '/^(\d{16})-([A-Za-z0-9_-]+)\.txt$/', (string) $name, $m ) )
        $list [] = [ 'id' => "$m[1]-$m[2]", 'time' => (int) substr ( $m [1], 0, 10 ),
                     'user' => $m [2], 'size' => (int) @filesize ( $dir . $name ) ];

    usort ( $list, fn ( $a, $b ) => strcmp ( $b ['id'], $a ['id'] ) );

    return $list;

  }

  function editHistoryGet ( $base, $app, $root, $path, $id ) {

    if ( ! preg_match ( '/^\d{16}-[A-Za-z0-9_-]+$/', (string) $id ) )
      editFail ( 'there is no such version' );

    $file = editHistoryDir ( $base, $app, $root, $path ) . "$id.txt";

    if ( ! is_file ( $file ) )
      editFail ( 'that version is gone' );

    return (string) file_get_contents ( $file );

  }

  // ---------------------------------------------------------------------------------------
  // Trash: a deleted file or directory is moved, not removed, into trash/<id>/item, with a
  // meta.json saying where it came from; it can be put back while that place is free. Items
  // older than $days go the next time something is deleted.
  // ---------------------------------------------------------------------------------------

  function editTrashPut ( $base, $app, $root, $path, $file, $user ) {

    if ( ! file_exists ( $file ) and ! is_link ( $file ) )
      editFail ( 'there is nothing to delete' );

    $id  = date ( 'YmdHis' ) . '-' . bin2hex ( random_bytes ( 4 ) );
    $dir = $base . "trash/$id/";

    editJsonWrite ( $dir . 'meta.json', [ 'app' => $app, 'root' => $root, 'path' => $path,
                                          'dir' => is_dir ( $file ), 'user' => $user, 'time' => time () ] );

    if ( ! @rename ( $file, $dir . 'item' ) ) {
      editCopyTree ( $file, $dir . 'item' );
      editRemoveTree ( $file );
    }

    return $id;

  }

  function editTrashList ( $base ) {

    $list = [];

    foreach ( (array) @scandir ( $base . 'trash' ) as $id )
      if ( preg_match ( '/^\d{14}-[0-9a-f]{8}$/', (string) $id ) ) {
        $meta = editJsonRead ( $base . "trash/$id/meta.json", NULL );
        if ( $meta )
          $list [] = [ 'id' => $id ] + $meta;
      }

    usort ( $list, fn ( $a, $b ) => strcmp ( $b ['id'], $a ['id'] ) );

    return $list;

  }

  function editTrashMeta ( $base, $id ) {

    if ( ! preg_match ( '/^\d{14}-[0-9a-f]{8}$/', (string) $id ) )
      editFail ( 'there is no such item in the trash' );

    $meta = editJsonRead ( $base . "trash/$id/meta.json", NULL );

    if ( ! $meta or ! file_exists ( $base . "trash/$id/item" ) )
      editFail ( 'that item is not in the trash anymore' );

    return $meta;

  }

  // $target is where the item goes back to, as editPath answered it for the meta's path.

  function editTrashRestore ( $base, $id, $target ) {

    editTrashMeta ( $base, $id );

    editMove ( $base . "trash/$id/item", $target );

    editRemoveTree ( $base . "trash/$id" );

  }

  function editTrashDrop ( $base, $id ) {

    editTrashMeta ( $base, $id );

    editRemoveTree ( $base . "trash/$id" );

  }

  function editTrashPurge ( $base, $days ) {

    $gone = 0;

    foreach ( editTrashList ( $base ) as $item )
      if ( ( $item ['time'] ?? 0 ) < time () - $days * 86400 ) {
        editRemoveTree ( $base . 'trash/' . $item ['id'] );
        $gone++;
      }

    return $gone;

  }

?>
