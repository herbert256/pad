<?php

  // Pipe function exists: treats the piped value as a path relative to the application
  // directory (APP) and returns the string '1' or '0' - a file test, not a value test.
  //
  // An empty value names no file - it named the application directory itself and answered
  // '1' - and a path that climbs out with .. stays outside: the test answered for any file
  // of the machine.

  $padExistsPath = trim ( (string) $value );

  if ( $padExistsPath === '' or preg_match ( '#(^|[/\\\\])\.\.([/\\\\]|$)#', $padExistsPath ) )
    return '0';

  return ( file_exists ( APP . ltrim ( $padExistsPath, '/' ) ) ) ? '1' : '0';

?>
