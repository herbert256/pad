<?php

  // A new application, made the way the pad command makes one (apps/cli/_commands/new.php):
  // apps/<name>/ with a first page pair and a README, and www/<name>/index.php.

  $name = trim ( editArg ( $body, 'name' ), '/' );

  if ( ! preg_match ( '/^[A-Za-z0-9][A-Za-z0-9_-]*(\/[A-Za-z0-9][A-Za-z0-9_-]*)*$/', $name ) )
    editFail ( 'an application name is letters, digits, _ and -, parts joined with /' );

  if ( file_exists ( APPS . $name ) or file_exists ( editHome () . "/www/$name" ) )
    editFail ( "apps/$name or www/$name exists already" );

  // An application is not made inside another: pad new takes a name under one for a page of
  // it - demo/admin made apps/demo/admin.php and admin.pad - and the call then failed, the
  // two files left in demo unasked. The application is found as pad new finds it: a leading
  // part of the name with an entry point in www/.

  $parts = explode ( '/', $name );

  for ( $i = 1; $i < count ( $parts ); $i++ ) {
    $part = implode ( '/', array_slice ( $parts, 0, $i ) );
    if ( file_exists ( editHome () . "/www/$part/index.php" ) and is_dir ( APPS . $part ) )
      editFail ( "$part is an application - one is not made inside another; New in its tree makes a page" );
  }

  [ $code, $out, $err ] = editRun ( [ editPhpBinary (), editHome () . '/apps/cli/pad', 'new', $name ], NULL, 20 );

  if ( $code !== 0 or ! is_dir ( APPS . $name ) )
    editFail ( 'pad new failed: ' . trim ( $err . ' ' . $out ) );

  return include APP . '_api/apps.php';

?>
