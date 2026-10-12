<?php

  // What the pages of the console share.
  //
  // adminBytes      a number of bytes for a person: 1.2 MB
  // adminWhen       a time as 2026-10-08 14:03, with how long ago
  // adminDirSize    [ bytes, files ] of everything below a directory
  // adminRemove     a directory and everything below it - only ever below DATA/
  // adminText       a text as HTML, an entity in it shown as written
  // adminPhpinfoBody phpinfo ()'s page without the request's headers and environment
  // adminGitLast    the last commit: hash, subject, author, time
  // adminLogToday   the lines every application logged today
  // adminRemoteShown a remote's address without its user and password
  // adminGitChanges the changes git status lists, as rows
  // adminRelative   a relative path a request names, checked: no dot segments, no hidden
  //                 names, nothing outside the characters a PAD file name uses; '' otherwise
  // adminRun        the pad command (apps/cli/pad) with arguments, in a child process:
  //                 [ exit code, output, milliseconds ] - none while $adminCommands is off
  // adminGit        git in the PAD home, the same way
  // adminRedact     source text with the values of passwords, keys and secrets left out
  // adminSearchLines  the lines of a text holding what is searched for, its secrets left out
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

  // A link is taken away itself, its own directory checked to be below DATA/: resolved, it
  // was followed - emptying DATA/cache deleted what a link there pointed to, a users file
  // or DATA/keys, and left the link.

  function adminRemove ( $path ) {

    $data = realpath ( DATA );

    if ( is_link ( $path ) )
      $real = ( $dir = realpath ( dirname ( $path ) ) ) === FALSE ? FALSE : $dir . DIRECTORY_SEPARATOR . basename ( $path );
    else
      $real = realpath ( $path );

    if ( $data === FALSE or $real === FALSE or ! str_starts_with ( $real, $data . DIRECTORY_SEPARATOR ) )
      return 0;

    if ( is_link ( $real ) or is_file ( $real ) )
      return @unlink ( $real ) ? 1 : 0;

    $count = 0;

    foreach ( scandir ( $real ) ?: [] as $item )
      if ( $item !== '.' and $item !== '..' )
        $count += adminRemove ( "$real/$item" );

    @rmdir ( $real );

    return $count;

  }

  // A text as HTML, every & of it written as &amp; - for the bytes of a file, printed with
  // {!...}: the sanitize chain of {$x} leaves an entity that is there alone, so a mail's
  // &lt;b&gt; read as <b> in its source and became an element in its sandboxed frame.

  function adminText ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // The body of phpinfo ()'s page, without what the request brought: under mod_php the
  // module tables hold the Apache environment and every request header - the console's own
  // session cookie, which is httponly, put in the page for any script to read, and the
  // secrets of a SetEnv. The masks of the call keep out only PHP's own lists of them.

  function adminPhpinfoBody ( $phpinfo ) {

    if ( ! preg_match ( '#<body[^>]*>(.*)</body>#s', $phpinfo, $match ) )
      return '<pre>' . adminText ( $phpinfo ) . '</pre>';

    return preg_replace ( '#<h2>(?:Apache Environment|HTTP Headers Information)</h2>\s*<table>.*?</table>#s', '', $match [1] ) ?? '';

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

  // With $adminCommands off nothing starts, whichever page asks - the queues' retry, flush
  // and work and the schedule's run and listing as well as the console, where only the
  // console looked at the switch and the others ran the command all the same.

  function adminRun ( $args, $timeout = NULL ) {

    global $adminCommands;

    if ( ! $adminCommands )
      return [ -1, 'The commands are switched off: $adminCommands in _config/config.php.', 0 ];

    return adminProcess ( array_merge ( [ adminPhp (), APPS . 'cli/pad' ], $args ), $timeout );

  }

  function adminGit ( $args ) {

    return adminProcess ( array_merge ( [ 'git', '-C', rtrim ( adminHome (), '/' ) ], $args ), 20 );

  }

  // A remote's address without the user and password it may hold: up to the last @, since
  // a password with a / or an @ in it - unencoded - ended the cut early and the rest of it
  // was shown.

  function adminRemoteShown ( $remote ) {

    return preg_replace ( '#//.*@#', '//', (string) $remote );

  }

  // The changes of git status --porcelain as rows: a line in that form alone - two letters
  // of state, a space, the file - since the output holds what git says on stderr too, and a
  // warning ("warning: in the working copy of ...") was listed as a change.

  function adminGitChanges ( $text ) {

    $rows = [];

    foreach ( explode ( "\n", (string) $text ) as $line )
      if ( preg_match ( '/^([ MADRCUT?!]{2}) (.+)$/', $line, $m ) )
        $rows [] = [ 'state' => trim ( $m [1] ), 'file' => $m [2],
                     'tone'  => match ( trim ( $m [1] ) ) { '??' => 'info', 'D' => 'bad', 'A' => 'ok', default => 'warn' } ];

    return $rows;

  }

  // The last commit: [ hash, subject, author, time ], empty without git. The fields are
  // parted by a unit separator, the subject last: parted by |, a subject with a pipe in it -
  // common in this repository - was cut there, its rest taken for the author.

  function adminGitLast ( $git = NULL ) {

    $git ??= adminGit ( [ 'log', '-1', '--format=%h%x1f%ct%x1f%an%x1f%s' ] );

    [ $hash, $time, $author, $subject ] = array_pad ( $git [0] === 0 ? explode ( "\x1f", trim ( $git [1] ), 4 ) : [], 4, '' );

    return [ $hash, $subject, $author, $time ];

  }

  // A process and every process below it, found before any is stopped: pad lint and pad test
  // start renders of their own, four at a time, and a timeout stopped only the command - the
  // renders went on, and a killed parent's children can no longer be found by it.

  function adminProcessTree ( $pid ) {

    $tree = [ $pid ];

    foreach ( preg_split ( '/\s+/', trim ( (string) @shell_exec ( 'pgrep -P ' . (int) $pid . ' 2>/dev/null' ) ) ) as $child )
      if ( ctype_digit ( $child ) )
        $tree = array_merge ( $tree, adminProcessTree ( (int) $child ) );

    return $tree;

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
        foreach ( array_reverse ( adminProcessTree ( (int) $status ['pid'] ) ) as $one )
          if ( $one != $status ['pid'] )
            function_exists ( 'posix_kill' ) ? @posix_kill ( $one, 9 ) : @exec ( 'kill -9 ' . (int) $one );
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

  // The words a name of a secret holds - one list for the sources and the settings.

  function adminRedactWords () {

    return 'password|passwd|pwd|pass|secret|token|apikey|api_key|appkey|app_key|credential|private|salt|cipher|key';

  }

  // The values of passwords, keys and secrets left out of source text, in the ways they are
  // written: a PHP variable, an array key or element, a property or a define () with a
  // quoted value - an escaped quote inside it read as part of it -, a JSON or YAML key, an
  // XML element, a name=value line of a .env, and the password of an address. A value that
  // is computed - an expression, a function's argument, a heredoc - is not recognised.

  function adminRedact ( $source ) {

    $w = '\w*(?:' . adminRedactWords () . ')\w*';

    // A quoted value as runs of plain characters with its escapes between, each run
    // possessive, per kind of quote: a group repeated per character ran out of PCRE's JIT
    // stack on a value of some ten thousand characters - a key, a certificate - and the
    // redaction answered nothing. A step that still fails shows nothing rather than the
    // text it could not clean.

    $q = '(?:(\')((?:[^\'\\\\]++|\\\\.)*+)\'|(")((?:[^"\\\\]++|\\\\.)*+)")';
    $r = '$1$2$4*REDACTED*$2$4';

    foreach ( [
      '/(\$' . $w . '\s*=\s*)' . $q . '/i'                        => $r,
      '/([\'"]' . $w . '[\'"]\s*=>\s*)' . $q . '/i'              => $r,
      '/(\[\s*[\'"]' . $w . '[\'"]\s*\]\s*=\s*)' . $q . '/i'     => $r,
      '/(->' . $w . '\s*=\s*)' . $q . '/i'                        => $r,
      '/(define\s*\(\s*[\'"]' . $w . '[\'"]\s*,\s*)' . $q . '/i' => $r,
      '/("' . $w . '"\s*:\s*)"(?:[^"\\\\]++|\\\\.)*+"/i'            => '$1"*REDACTED*"',
      '/^(\s*-?\s*' . $w . '\s*:[ \t]+)(?!\*REDACTED\*)(\S.*)$/im' => '$1*REDACTED*',
      '/(<(' . $w . ')\b[^>]*>)[^<]*(<\/\2>)/i'                   => '$1*REDACTED*$3',
      '/^(\s*' . $w . '\s*=\s*)(\S.*)$/im'                         => '$1*REDACTED*',
      '#(\b[a-z][a-z0-9+.-]*://[^/\s:@\'"]*:)[^@\s\'"/]+(@)#i'     => '$1*REDACTED*$2'
    ] as $pattern => $replace )
      if ( ( $source = preg_replace ( $pattern, $replace, (string) $source ) ) === NULL )
        return '';

    return $source;

  }

  function adminRedactValue ( $name, $value ) {

    if ( preg_match ( '/' . adminRedactWords () . '/i', $name ) and $value !== '' and $value !== NULL )
      return '*REDACTED*';

    return $value;

  }

  // The lines of a text that hold what is searched for - line number => line - searched in
  // the text as the console shows it, its secrets left out: matched against the raw text,
  // a search for $myToken = 'ab found the line where $myToken = 'ax did not, and one search
  // per character spelled out the value the line it showed hid.

  function adminSearchLines ( $text, $for, $case ) {

    $lines = [];
    $text  = adminRedact ( $text );

    if ( ( $case ? strpos ( $text, $for ) : stripos ( $text, $for ) ) === FALSE )
      return $lines;

    foreach ( explode ( "\n", $text ) as $number => $line )
      if ( ( $case ? strpos ( $line, $for ) : stripos ( $line, $for ) ) !== FALSE )
        $lines [$number] = $line;

    return $lines;

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

  // Taking an application down or up from the console, under the rules of pad down: the
  // secret a valid one, and the message what happened - it said "is down" when nothing was
  // written, and "is up again" of one that was not down.

  function adminMaintenance ( $app, $action, $page, $vars = [] ) {

    if ( $action == 'down' ) {

      $secret = trim ( adminField ( 'secret' ) );

      if ( $secret !== '' and ! padMaintenanceSecretValid ( $app, $secret ) )
        adminDone ( 'A secret is 6 to 64 letters, digits, _ and -, and not the name of a page of the application.', $page, $vars, 'error' );

      if ( ! padMaintenanceDown ( $app, $secret, (int) adminField ( 'retry', '60' ), adminField ( 'message' ) ) )
        adminDone ( "$app could not be taken down: DATA/maintenance/ cannot be written.", $page, $vars, 'error' );

      adminDone ( "$app is down for maintenance.", $page, $vars );

    }

    if ( $action == 'up' ) {

      if ( ! padMaintenanceUp ( $app ) )
        adminDone ( "$app was not down.", $page, $vars, 'error' );

      adminDone ( "$app is up again.", $page, $vars );

    }

  }

  // The lines logged today by every application, its log found by its whole name as padLog
  // writes it: a glob one directory deep missed regression/queue and every other name with
  // a / in it.

  function adminLogToday () {

    $lines = 0;

    foreach ( array_keys ( adminApps () ) as $app )
      if ( is_file ( $file = DATA . "logs/$app/" . date ( 'Y-m-d' ) . '.log' ) )
        $lines += substr_count ( (string) @file_get_contents ( $file ), "\n" );

    return $lines;

  }

  // The application a maintenance post names: one there is, or - to bring it up - one whose
  // record is still there: the record of an application that is gone, renamed or removed,
  // was listed, but its "Bring up" was refused as no application.

  function adminMaintenanceApp ( $action ) {

    $app = adminAppAsked ();

    if ( $app === '' and $action == 'up' and isset ( padMaintenanceList () [ adminField ( 'app' ) ] ) )
      $app = adminField ( 'app' );

    return $app;

  }

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

  // Read, added to and written under a lock: two commands that ended together each wrote the
  // list it had read, and a line was lost. Readable by its owner only, as the users are.

  function adminHistoryAdd ( $line, $code, $ms ) {

    global $adminUser;

    $dir = adminStore ();

    if ( ! is_dir ( $dir ) )
      @mkdir ( $dir, 0700, TRUE );

    $lock = @fopen ( $dir . 'history.lock', 'c' );

    if ( $lock )
      flock ( $lock, LOCK_EX );

    try {

      $list = json_decode ( (string) @file_get_contents ( $dir . 'history.json' ), TRUE );
      $list = is_array ( $list ) ? $list : [];

      array_unshift ( $list, [ 'time' => time (), 'user' => (string) $adminUser, 'line' => $line, 'code' => $code, 'ms' => $ms ] );

      if ( ! is_file ( $dir . 'history.json' ) and @touch ( $dir . 'history.json' ) )
        @chmod ( $dir . 'history.json', 0600 );

      @file_put_contents ( $dir . 'history.json', json_encode ( array_slice ( $list, 0, 100 ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), LOCK_EX );

    } finally {

      if ( $lock ) {
        flock  ( $lock, LOCK_UN );
        fclose ( $lock );
      }

    }

  }

?>
