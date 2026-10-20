<?php

  // The entries of a directory of the engine or an application, kept between requests.
  // Reading a directory is one of the dearest things a request does - 40 to 100 us for
  // pad/lib/ on this Mac, where a stat of it takes under one - and every request read the
  // same ones: pad/lib/ and its subdirectories to load the library, at/_lib/, tags/_lib/ for
  // the first built-in tag, and the _lib of each directory of the page.
  //
  // padDirList   what scandir answers: sorted, . and .. included
  // padDirRaw    the entries in the order the directory hands them out, without . and ..,
  //              each with whether it is a directory (filetype, so a link to a directory is
  //              not one) - the order padDirFiles walks an application's _lib in
  //
  // A listing is kept with the directory's inode and times, and read again when one of them
  // changed - an entry added, removed or renamed changes the modification time, and the
  // change time moves on too when a tool that copies a tree (rsync, tar) sets the first
  // back. A directory that changed within the last two seconds is not kept: a time has
  // whole seconds, and an entry added later in that second would have left it the same.
  //
  // The listings stand in DATA/cache/dirs/: those of the engine's directories in pad.json,
  // the others in a file of the application the request is for - each read once for the
  // request and written again only when a listing in it changed, so what a request reads
  // stays a few kilobytes. Without a writable DATA every request reads the directories, as
  // before. Included by inits/lib.php before the rest of the library, which it lists.

  function padDirList ( $dir ) {

    $kept = padDirKept ( 'S', $dir, $stat );

    if ( $kept !== NULL )
      return $kept;

    $list = scandir ( $dir );

    if ( $stat and $list !== FALSE )
      padDirKeep ( 'S', $dir, $stat, $list );

    return $list;

  }

  function padDirRaw ( $dir ) {

    $kept = padDirKept ( 'R', $dir, $stat );

    if ( $kept !== NULL )
      return $kept;

    $entries = [];

    if ( ! ( $handle = @opendir ( $dir ) ) )
      return $entries;

    while ( ( $entry = readdir ( $handle ) ) !== FALSE )
      if ( $entry !== '.' and $entry !== '..' )
        $entries [] = $entry;

    closedir ( $handle );

    $list = [];

    foreach ( $entries as $entry )
      $list [] = [ $entry, @filetype ( "$dir/$entry" ) === 'dir' ];

    if ( $stat )
      padDirKeep ( 'R', $dir, $stat, $list );

    return $list;

  }

  // The kept listing when the directory is as it was, NULL when not; $stat is then what is
  // to be kept with a new listing, or FALSE when the directory is not to be kept now.

  function padDirKept ( $kind, $dir, &$stat ) {

    $stat = FALSE;
    $now  = @stat ( $dir );

    if ( ! $now )
      return NULL;

    $kept = padDirStore ( $dir ) ["$kind:$dir"] ?? NULL;

    if ( $kept and $kept [0] === $now ['ino'] and $kept [1] === $now ['mtime'] and $kept [2] === $now ['ctime'] )
      return $kept [3];

    if ( time () - max ( $now ['mtime'], $now ['ctime'] ) >= 2 )
      $stat = [ $now ['ino'], $now ['mtime'], $now ['ctime'] ];

    return NULL;

  }

  function padDirKeep ( $kind, $dir, $stat, $list ) {

    $store = & padDirStore ( $dir );
    $file  = padDirFile ( $dir );

    $store ["$kind:$dir"] = [ $stat [0], $stat [1], $stat [2], $list ];

    // What is written is what is read by every request: a directory that is gone - a test's
    // scratch application - goes out of it.

    foreach ( $store as $key => $unused )
      if ( ! is_dir ( substr ( $key, 2 ) ) )
        unset ( $store [$key] );

    $json = json_encode ( $store, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

    if ( $json === FALSE or ! ( is_dir ( DATA . 'cache/dirs' ) or @mkdir ( DATA . 'cache/dirs', 0777, TRUE ) ) )
      return;

    $tmp = $file . '.' . getmypid () . '.' . bin2hex ( random_bytes ( 4 ) );

    if ( @file_put_contents ( $tmp, $json ) === strlen ( $json ) and @rename ( $tmp, $file ) )
      return;

    @unlink ( $tmp );

  }

  // The file a directory's listing stands in: pad.json for the engine's, the request's
  // application's for any other - regression/framework in regression.framework.json.

  function padDirFile ( $dir ) {

    static $app = NULL;

    if ( str_starts_with ( $dir, PAD ) )
      return DATA . 'cache/dirs/pad.json';

    if ( $app === NULL ) {
      $name = ( defined ( 'APP' ) and defined ( 'APPS' ) and str_starts_with ( APP, APPS ) ) ? substr ( APP, strlen ( APPS ), -1 ) : '';
      $app  = DATA . 'cache/dirs/app.' . ( preg_match ( '#^\w[\w/-]*$#', $name ) ? str_replace ( '/', '.', $name ) : md5 ( defined ( 'APP' ) ? APP : '' ) ) . '.json';
    }

    return $app;

  }

  function & padDirStore ( $dir ) {

    static $stores = [];

    $file = padDirFile ( $dir );

    if ( ! isset ( $stores [$file] ) ) {
      $stores [$file] = json_decode ( (string) @file_get_contents ( $file ), TRUE );
      if ( ! is_array ( $stores [$file] ) )
        $stores [$file] = [];
    }

    return $stores [$file];

  }

?>
