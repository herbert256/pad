<?php

  // Which applications there are, where their files live, and which names inside them the
  // editor may touch. Every path a call names goes through editPath, which answers the
  // absolute path or refuses - by exception, which api.php turns into the call's error.
  //
  // editHome   the checkout: the directory holding apps/, www/ and pad/
  // editApps   every application - a directory under apps/ with an entry point
  //            www/<app>/index.php, nested ones like regression/main too - and _common
  // editRoots  the roots of an application: 'app' (apps/<app>/), and 'www' (www/<app>/,
  //            its stylesheets and scripts) when it has one
  // editRoot   one root, as a real path ending in /
  // editPath   a path inside a root, refused when it leaves it: a .. or empty segment, a
  //            control character or backslash, .git, or a symbolic link pointing out

  function editHome () {

    return dirname ( APPS );

  }

  function editFail ( $message ) {

    throw new RuntimeException ( $message );

  }

  // The walk does not go into an application - one never holds another - nor into the
  // underscore directories of a namespace; regression/ is a namespace: no entry point of
  // its own, applications below it.

  function editApps () {

    static $apps = NULL;

    if ( $apps !== NULL )
      return $apps;

    $apps = [ '_common' => '_common' ];

    editAppsWalk ( '', 0, $apps );

    ksort ( $apps, SORT_STRING | SORT_FLAG_CASE );

    return $apps;

  }

  function editAppsWalk ( $prefix, $depth, &$apps ) {

    if ( $depth > 3 )
      return;

    $dir  = APPS . $prefix;
    $list = @scandir ( $dir );

    if ( ! $list )
      return;

    foreach ( $list as $one ) {

      if ( $one [0] == '.' or $one [0] == '_' or ! is_dir ( "$dir$one" ) )
        continue;

      $name = $prefix . $one;

      if ( file_exists ( editHome () . "/www/$name/index.php" ) )
        $apps [$name] = $name;
      else
        editAppsWalk ( "$name/", $depth + 1, $apps );

    }

  }

  function editApp ( $app ) {

    $app = (string) $app;

    if ( ! isset ( editApps () [$app] ) )
      editFail ( "there is no application named '" . padMakeSafe ( $app, 60 ) . "'" );

    return $app;

  }

  function editRoots ( $app ) {

    $app   = editApp ( $app );
    $roots = [ 'app' => APPS . $app ];

    if ( $app != '_common' and is_dir ( editHome () . "/www/$app" ) )
      $roots ['www'] = editHome () . "/www/$app";

    return $roots;

  }

  function editRoot ( $app, $root ) {

    $roots = editRoots ( $app );

    if ( ! isset ( $roots [$root] ) )
      editFail ( "the application '$app' has no root named '" . padMakeSafe ( (string) $root, 20 ) . "'" );

    $real = realpath ( $roots [$root] );

    if ( $real === FALSE )
      editFail ( "the directory of '$app' is gone" );

    return "$real/";

  }

  // A relative path, '' for the root itself. It is checked as text first, then against the
  // disk: the nearest part of it that exists - the path itself, or the directory a new file
  // goes into - must resolve inside the root, so a symbolic link cannot lead out of it.

  function editPath ( $app, $root, $rel ) {

    $base = editRoot ( $app, $root );
    $rel  = (string) $rel;

    if ( $rel === '' )
      return $base;

    if ( strlen ( $rel ) > 1024 )
      editFail ( 'the path is too long' );

    if ( preg_match ( '/[\x00-\x1F\x7F\\\\]/', $rel ) )
      editFail ( 'a path cannot hold a control character or a backslash' );

    foreach ( explode ( '/', $rel ) as $part ) {

      if ( $part === '' or $part === '.' or $part === '..' )
        editFail ( "the path '" . padMakeSafe ( $rel, 80 ) . "' leaves its directory or has an empty part" );

      if ( strtolower ( $part ) === '.git' )
        editFail ( 'the .git directory is not edited here' );

      if ( strlen ( $part ) > 255 )
        editFail ( 'a name in the path is too long' );

    }

    $path  = $base . $rel;
    $probe = $path;

    while ( ! file_exists ( $probe ) and ! is_link ( $probe ) and strlen ( $probe ) > strlen ( $base ) )
      $probe = dirname ( $probe );

    $real = realpath ( $probe );

    if ( $real === FALSE or ! str_starts_with ( "$real/", $base ) )
      editFail ( "the path '" . padMakeSafe ( $rel, 80 ) . "' leads out of the application" );

    return $path;

  }

  // The name of a new file or directory, on its own: one segment, nothing a path could
  // make more of.

  function editName ( $name ) {

    $name = trim ( (string) $name );

    if ( $name === '' or $name === '.' or $name === '..' or str_contains ( $name, '/' )
         or preg_match ( '/[\x00-\x1F\x7F\\\\]/', $name ) or strlen ( $name ) > 255 )
      editFail ( "'" . padMakeSafe ( $name, 60 ) . "' is not a file name" );

    if ( strtolower ( $name ) === '.git' )
      editFail ( 'the .git directory is not edited here' );

    return $name;

  }

  // The path of an absolute file, relative to its root - for answers.

  function editRel ( $base, $path ) {

    return ltrim ( substr ( $path, strlen ( $base ) ), '/' );

  }

?>
