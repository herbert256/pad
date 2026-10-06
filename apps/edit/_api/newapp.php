<?php

  // A new application, made the way the pad command makes one (apps/cli/_commands/new.php):
  // apps/<name>/ with a first page pair and a README, and www/<name>/index.php.

  $name = trim ( editArg ( $body, 'name' ), '/' );

  if ( ! preg_match ( '/^[A-Za-z0-9][A-Za-z0-9_-]*(\/[A-Za-z0-9][A-Za-z0-9_-]*)*$/', $name ) )
    editFail ( 'an application name is letters, digits, _ and -, parts joined with /' );

  if ( file_exists ( APPS . $name ) or file_exists ( editHome () . "/www/$name" ) )
    editFail ( "apps/$name or www/$name exists already" );

  [ $code, $out, $err ] = editRun ( [ editPhpBinary (), editHome () . '/apps/cli/pad', 'new', $name ], NULL, 20 );

  if ( $code !== 0 or ! is_dir ( APPS . $name ) )
    editFail ( 'pad new failed: ' . trim ( $err . ' ' . $out ) );

  return include APP . '_api/apps.php';

?>
