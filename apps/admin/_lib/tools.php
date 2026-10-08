<?php

  // What the pages of the console share.
  //
  // adminBytes      a number of bytes for a person: 1.2 MB
  // adminWhen       a time as 2026-10-08 14:03, with how long ago
  // adminDirSize    [ bytes, files ] of everything below a directory
  // adminRemove     a directory and everything below it - only ever below DATA/
  // adminRelative   a relative path a request names, checked: no dot segments, no hidden
  //                 names, nothing outside the characters a PAD file name uses; '' otherwise
  // adminRun        the pad command (apps/cli/pad) with arguments, in a child process:
  //                 [ exit code, output, milliseconds ]
  // adminGit        git in the PAD home, the same way
  // adminRedact     source text with the values of passwords, keys and secrets left out
  // adminTail       the last lines of a file, read from its end
  // adminDone       a flash message and a redirect - the end of every post

  function adminBytes ( $bytes ) {

    $bytes = (float) $bytes;
    $units = [ 'B', 'KB', 'MB', 'GB', 'TB' ];
    $unit  = 0;

    while ( $bytes >= 1024 and $unit < count ( $units ) - 1 ) {
      $bytes /= 1024;
      $unit++;
    }

    return ( $unit == 0 ? (int) $bytes : number_format ( $bytes, $bytes < 10 ? 1 : 0 ) ) . ' ' . $units [$unit];

  }

  function adminWhen ( $time ) {

    $time = (int) $time;

    if ( ! $time )
      return '';

    return date ( 'Y-m-d H:i', $time );

  }

  function adminAgo ( $time ) {

    $time = (int) $time;

    if ( ! $time )
      return '';

    $diff = time () - $time;

    if ( $diff < 60 )       return 'just now';
    if ( $diff < 3600 )     return floor ( $diff / 60 ) . ' min ago';
    if ( $diff < 86400 )    return floor ( $diff / 3600 ) . ' h ago';
    if ( $diff < 86400*60 ) return floor ( $diff / 86400 ) . ' days ago';

    return floor ( $diff / ( 86400 * 30 ) ) . ' months ago';

  }

  function adminDirSize ( $dir ) {

    $bytes = $files = 0;

    if ( ! is_dir ( $dir ) )
      return [ 0, 0 ];

    try {

      $walk = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $dir, FilesystemIterator::SKIP_DOTS ) );

      foreach ( $walk as $one )
        if ( $one->isFile () ) {
          $bytes += $one->getSize ();
          $files++;
        }

    } catch ( Throwable $e ) { }

    return [ $bytes, $files ];

  }

  function adminRemove ( $path ) {

    $data = realpath ( DATA );
    $real = realpath ( $path );

    if ( $data === FALSE or $real === FALSE or ! str_starts_with ( $real, $data . DIRECTORY_SEPARATOR ) )
      return 0;

    if ( is_file ( $real ) or is_link ( $real ) )
      return @unlink ( $real ) ? 1 : 0;

    $count = 0;

    foreach ( scandir ( $real ) ?: [] as $item )
      if ( $item !== '.' and $item !== '..' )
        $count += adminRemove ( "$real/$item" );

    @rmdir ( $real );

    return $count;

  }

  function adminRelative ( $path ) {

    $path = trim ( is_string ( $path ) ? $path : '', '/' );

    if ( $path === '' )
      return '';

    if ( ! preg_match ( '#^[A-Za-z0-9_\-\[\]+.]+(/[A-Za-z0-9_\-\[\]+.]+)*$#D', $path ) )
      return '';

    foreach ( explode ( '/', $path ) as $part )
      if ( $part [0] === '.' )
        return '';

    return $path;

  }

  function adminPhp () {

    if ( PHP_BINARY !== '' and preg_match ( '/^php[0-9.]*(\.exe)?$/i', basename ( PHP_BINARY ) ) )
      return PHP_BINARY;

    return is_executable ( PHP_BINDIR . '/php' ) ? PHP_BINDIR . '/php' : 'php';

  }

  function adminRun ( $args, $timeout = NULL ) {

    return adminProcess ( array_merge ( [ adminPhp (), APPS . 'cli/pad' ], $args ), $timeout );

  }

  function adminGit ( $args ) {

    return adminProcess ( array_merge ( [ 'git', '-C', rtrim ( adminHome (), '/' ) ], $args ), 20 );

  }

  function adminProcess ( $command, $timeout = NULL ) {

    global $adminTimeout;

    $timeout = (int) ( $timeout ?? $adminTimeout ?? 120 );
    $start   = microtime ( TRUE );
    $env     = array_merge ( getenv (), [ 'PAD_HOME' => rtrim ( adminHome (), '/' ), 'NO_COLOR' => '1' ] );

    @set_time_limit ( $timeout + 30 );

    $proc = @proc_open ( $command, [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ],
                         $pipes, rtrim ( adminHome (), '/' ), $env );

    if ( ! is_resource ( $proc ) )
      return [ -1, 'The process could not be started.', 0 ];

    stream_set_blocking ( $pipes [1], FALSE );
    stream_set_blocking ( $pipes [2], FALSE );

    $out = '';

    while ( TRUE ) {

      $read  = [ $pipes [1], $pipes [2] ];
      $write = $except = NULL;

      if ( @stream_select ( $read, $write, $except, 0, 200000 ) )
        foreach ( $read as $pipe )
          $out .= (string) fread ( $pipe, 65536 );

      $status = proc_get_status ( $proc );

      if ( ! $status ['running'] )
        break;

      if ( microtime ( TRUE ) - $start > $timeout ) {
        proc_terminate ( $proc, 9 );
        $out .= "\n[stopped after $timeout seconds]";
        $status = [ 'exitcode' => -1 ];
        break;
      }

    }

    $out .= (string) stream_get_contents ( $pipes [1] ) . (string) stream_get_contents ( $pipes [2] );

    fclose ( $pipes [1] );
    fclose ( $pipes [2] );

    $code = proc_close ( $proc );

    if ( ( $status ['exitcode'] ?? -1 ) !== -1 )
      $code = $status ['exitcode'];

    return [ $code, rtrim ( $out ), (int) round ( ( microtime ( TRUE ) - $start ) * 1000 ) ];

  }

  function adminRedact ( $source ) {

    $words = 'password|passwd|pwd|pass|secret|token|apikey|api_key|appkey|app_key|credential|private|salt|cipher|key';

    // $padSqlPassword = '...';  'password' => '...';  padSqlPassword=...  (a .env-like line)

    $source = preg_replace ( '/(\$\w*(?:' . $words . ')\w*\s*=\s*)([\'"])(.+?)\2/i', '$1$2*REDACTED*$2', $source );
    $source = preg_replace ( '/([\'"]\w*(?:' . $words . ')\w*[\'"]\s*=>\s*)([\'"])(.+?)\2/i', '$1$2*REDACTED*$2', $source );
    $source = preg_replace ( '/^(\s*\w*(?:' . $words . ')\w*\s*=\s*)(\S.*)$/im', '$1*REDACTED*', $source );

    return $source;

  }

  function adminRedactValue ( $name, $value ) {

    if ( preg_match ( '/password|passwd|secret|token|key|salt|credential/i', $name ) and $value !== '' and $value !== NULL )
      return '*REDACTED*';

    return $value;

  }

  function adminTail ( $file, $lines ) {

    $size = (int) @filesize ( $file );
    $read = min ( $size, max ( 65536, $lines * 400 ) );
    $hand = @fopen ( $file, 'r' );

    if ( ! $hand )
      return [];

    fseek ( $hand, $size - $read );

    $text = (string) fread ( $hand, $read );

    fclose ( $hand );

    $list = explode ( "\n", rtrim ( $text, "\n" ) );

    if ( $read < $size )
      array_shift ( $list );

    return array_slice ( $list, - $lines );

  }

  // A value printed in <pre>: PAD's own value escaping takes care of the HTML.

  function adminDone ( $message, $page, $vars = [], $type = 'ok' ) {

    padFlash ( $message, $type );
    padRedirect ( $page, $vars );

  }

  function adminPost () {

    return padRequestIs ( 'POST' );

  }

  // Value of a posted field, always a string.

  function adminField ( $name, $default = '' ) {

    $value = $_POST [$name] ?? $default;

    return is_string ( $value ) ? $value : $default;

  }

  function adminGet ( $name, $default = '' ) {

    $value = $_GET [$name] ?? $default;

    return is_string ( $value ) ? $value : $default;

  }

  // The console's history: the last hundred commands run from it, newest first.

  function adminHistory () {

    $list = json_decode ( (string) @file_get_contents ( adminStore () . 'history.json' ), TRUE );

    if ( ! is_array ( $list ) )
      return [];

    foreach ( $list as &$one )
      $one ['when'] = adminWhen ( $one ['time'] ?? 0 );

    return $list;

  }

  function adminHistoryAdd ( $line, $code, $ms ) {

    global $adminUser;

    $list = json_decode ( (string) @file_get_contents ( adminStore () . 'history.json' ), TRUE );
    $list = is_array ( $list ) ? $list : [];

    array_unshift ( $list, [ 'time' => time (), 'user' => (string) $adminUser, 'line' => $line, 'code' => $code, 'ms' => $ms ] );

    if ( ! is_dir ( adminStore () ) )
      @mkdir ( adminStore (), 0700, TRUE );

    @file_put_contents ( adminStore () . 'history.json', json_encode ( array_slice ( $list, 0, 100 ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), LOCK_EX );

  }

?>
