<?php

  // The database channel's store: the notifications kept on the site, a JSON file per
  // recipient under DATA/notifications/<application>/, named by a hash of the recipient - its
  // id, or its email. No table: an application without a database has it, an offline one
  // (pad pack) keeps it with its DATA/. A file holds the newest 500, each:
  //
  //   id       16 hex digits, random     name     the notification's template
  //   title    the title                 text     the text, html the HTML
  //   url      the message's url         data     what padNotify was handed
  //   created  a Unix time (padNow)      read     the Unix time it was read, or NULL
  //
  // A change is made under a lock of its own (flock on <file>.lock) and written beside the
  // file and renamed over it (padFilePut), so two requests that add at once both keep theirs.
  //
  // padNotifyStoreKey   the recipient's key: NULL is the user logged in, a row its id (or
  //                     email), a text or a number itself - NULL when there is none (a guest)
  // padNotifyStoreAdd   keeps one message for a recipient
  // padNotifyStoreList  a recipient's notifications, newest first - the unread ones, or all
  // padNotifyStoreRead  marks one read by its id, or all of them with 'all'
  // padNotifyStoreSign  the signature of a mark-read link: an HMAC of the application, the
  //                     recipient and the id with the application key - a link made for one
  //                     person marks nothing of another's, and no other site can make one

  const padNotifyStoreKeep = 500;

  function padNotifyStoreKey ( $to ) {

    if ( $to === NULL )
      $to = padUserId ();

    if ( is_object ( $to ) )
      $to = padToArray ( $to );

    if ( is_array ( $to ) )
      $to = $to ['id'] ?? $to ['email'] ?? NULL;

    return ( ( is_string ( $to ) or is_int ( $to ) ) and (string) $to !== '' ) ? (string) $to : NULL;

  }

  function padNotifyStoreFile ( $key ) {

    $app = trim ( (string) ( $GLOBALS ['padApp'] ?? 'app' ), '/' );

    return DATA . "notifications/$app/" . substr ( hash ( 'sha256', $key ), 0, 32 ) . '.json';

  }

  function padNotifyStoreLoad ( $file ) {

    $list = is_file ( $file ) ? json_decode ( (string) file_get_contents ( $file ), TRUE ) : [];

    return is_array ( $list ) ? array_values ( array_filter ( $list, 'is_array' ) ) : [];

  }

  // A change of a recipient's file under its lock: $change gets the list and answers the new
  // one, or FALSE to write nothing.

  function padNotifyStoreChange ( $key, $change ) {

    global $padDirMode;

    $file = padNotifyStoreFile ( $key );
    $dir  = dirname ( $file );

    if ( ! is_dir ( $dir ) and ! @mkdir ( $dir, $padDirMode ?? 0755, TRUE ) and ! is_dir ( $dir ) )
      return FALSE;

    $lock = @fopen ( "$file.lock", 'c' );

    if ( $lock === FALSE )
      return FALSE;

    try {

      flock ( $lock, LOCK_EX );

      $list = $change ( padNotifyStoreLoad ( $file ) );

      return ( $list === FALSE ) ? FALSE
           : padFilePut ( $file, json_encode ( array_values ( $list ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) ) === TRUE;

    } finally {

      flock ( $lock, LOCK_UN );
      fclose ( $lock );

    }

  }

  function padNotifyStoreAdd ( $key, $message ) {

    $key = padNotifyStoreKey ( $key );

    if ( $key === NULL )
      return FALSE;

    $record = [ 'id'      => bin2hex ( random_bytes ( 8 ) ),
                'name'    => (string) $message ['name'],
                'title'   => (string) $message ['title'],
                'text'    => (string) $message ['text'],
                'html'    => (string) $message ['html'],
                'url'     => (string) $message ['url'],
                'data'    => $message ['data'],
                'created' => (int) padNow ( 'U' ),
                'read'    => NULL ];

    return padNotifyStoreChange ( $key, fn ( $list ) => array_slice ( [ ...$list, $record ], - padNotifyStoreKeep ) );

  }

  function padNotifyStoreList ( $to = NULL, $all = FALSE ) {

    $key = padNotifyStoreKey ( $to );

    if ( $key === NULL )
      return [];

    $list = array_reverse ( padNotifyStoreLoad ( padNotifyStoreFile ( $key ) ) );

    return $all ? $list : array_values ( array_filter ( $list, fn ( $one ) => empty ( $one ['read'] ) ) );

  }

  function padNotifyStoreRead ( $id, $to = NULL ) {

    $key = padNotifyStoreKey ( $to );

    if ( $key === NULL or ! is_string ( $id ) or ( $id !== 'all' and ! preg_match ( '/^[0-9a-f]{16}$/D', $id ) ) )
      return FALSE;

    $found = FALSE;
    $now   = (int) padNow ( 'U' );

    $done = padNotifyStoreChange ( $key, function ( $list ) use ( $id, $now, &$found ) {
      foreach ( $list as $at => $one )
        if ( ( $id === 'all' or ( $one ['id'] ?? '' ) === $id ) and empty ( $one ['read'] ) ) {
          $list [$at] ['read'] = $now;
          $found = TRUE;
        }
      return $found ? $list : FALSE;
    } );

    return $done and $found;

  }

  function padNotifyStoreSign ( $key, $id ) {

    $secret = padCryptKey ( 'notifications' );

    return ( $secret === NULL ) ? '' : substr ( hash_hmac ( 'sha256', ( $GLOBALS ['padApp'] ?? '' ) . "|$key|$id", $secret ), 0, 24 );

  }

?>
