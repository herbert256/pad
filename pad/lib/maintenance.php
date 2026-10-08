<?php

  // Maintenance mode: an application taken down for a while - a migration, a deploy -
  // answers every web request 503 with a Retry-After, while the developer who holds the
  // secret goes on using it.
  //
  //   pad down shop --secret=letmein --retry=120 --message='Back at ten'
  //   pad up shop
  //   pad down                       the applications that are down
  //
  // pad down writes DATA/maintenance/<app>.json; while it is there the application is down.
  // The answer is the application's error page for 503 when it has one (_errors/503.pad,
  // lib/errorPage.php) with the message as $message, else the message as one plain line -
  // 'Service Unavailable' when none was given. It is never cached, and it comes before the
  // page cache and before any of the application runs (inits/maintenance.php), for a page
  // that is not there as well. A request asking ?<secret> - the address of the application
  // with the secret as its page - gets a cookie that lets that browser through for twelve
  // hours and is sent to the application's front page; a new pad down makes every earlier
  // cookie worthless. The secret itself is not kept, only its hash. The command line - pad
  // render, pad test - is never held back.
  //
  // The file functions are plain PHP, as the pad command uses them without the engine:
  //
  // padMaintenanceFile    the file of an application
  // padMaintenanceRead    its record - time, retry, message, secret (a hash) - or NULL
  // padMaintenanceDown    takes an application down: writes the record
  // padMaintenanceUp      brings it back: removes it
  // padMaintenanceList    the applications that are down, with their records
  //
  // and for the engine:
  //
  // padMaintenanceFileExists  whether the request's application is down - asked before the
  //                           configuration is read (inits/page.php)
  // padMaintenance            the gate: NULL when the application is up or the visitor
  //                           holds the cookie, the cookie and a redirect for the secret,
  //                           else the record of the application that is down
  // padMaintenanceRefuse      the 503 for that record, with its Retry-After
  // padMaintenanceCookie      the name of the application's bypass cookie
  // padMaintenanceToken       the value it holds for one pad down

  function padMaintenanceFile ( $app ) {

    $app = (string) $app;

    if ( ! preg_match ( '#^[A-Za-z0-9][A-Za-z0-9_-]*(/[A-Za-z0-9][A-Za-z0-9_-]*)*$#D', $app ) )
      return '';

    return DATA . "maintenance/$app.json";

  }

  function padMaintenanceRead ( $app ) {

    $file = padMaintenanceFile ( $app );

    if ( $file === '' or ! is_file ( $file ) )
      return NULL;

    $down = json_decode ( (string) @file_get_contents ( $file ), TRUE );

    // A file that is there is a request to be down, whatever it holds: one that cannot be
    // read is down without a secret rather than up.

    if ( ! is_array ( $down ) )
      $down = [];

    return [
      'time'    => (int)    ( $down ['time']    ?? 0  ),
      'retry'   => (int)    ( $down ['retry']   ?? 0  ),
      'message' => (string) ( $down ['message'] ?? '' ),
      'secret'  => (string) ( $down ['secret']  ?? '' )
    ];

  }

  function padMaintenanceDown ( $app, $secret = '', $retry = 60, $message = '' ) {

    $file = padMaintenanceFile ( $app );

    if ( $file === '' )
      return FALSE;

    if ( ! is_dir ( dirname ( $file ) ) )
      @mkdir ( dirname ( $file ), 0755, TRUE );

    $down = [
      'time'    => time (),
      'retry'   => max ( 0, (int) $retry ),
      'message' => (string) $message,
      'secret'  => (string) $secret === '' ? '' : hash ( 'sha256', (string) $secret )
    ];

    return file_put_contents ( $file, json_encode ( $down, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), LOCK_EX ) !== FALSE;

  }

  function padMaintenanceUp ( $app ) {

    $file = padMaintenanceFile ( $app );

    if ( $file === '' or ! is_file ( $file ) )
      return FALSE;

    return unlink ( $file );

  }

  function padMaintenanceList () {

    $dir  = DATA . 'maintenance/';
    $list = [];

    if ( ! is_dir ( $dir ) )
      return $list;

    $walk = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $dir, FilesystemIterator::SKIP_DOTS ) );

    foreach ( $walk as $one ) {

      if ( ! $one->isFile () or $one->getExtension () != 'json' )
        continue;

      $app = substr ( str_replace ( '\\', '/', $one->getPathname () ), strlen ( $dir ), -5 );

      if ( $down = padMaintenanceRead ( $app ) )
        $list [$app] = $down;

    }

    ksort ( $list );

    return $list;

  }

  function padMaintenanceFileExists () {

    $file = padMaintenanceFile ( $GLOBALS ['padApp'] ?? '' );

    return $file !== '' and is_file ( $file );

  }

  function padMaintenanceCookie () {

    return 'padMaintenance' . substr ( md5 ( (string) ( $GLOBALS ['padApp'] ?? '' ) ), 0, 8 );

  }

  function padMaintenanceToken ( $down ) {

    return hash_hmac ( 'sha256', ( $GLOBALS ['padApp'] ?? '' ) . '|' . $down ['time'], $down ['secret'] );

  }

  function padMaintenance () {

    if ( PHP_SAPI === 'cli' )
      return NULL;

    $down = padMaintenanceRead ( $GLOBALS ['padApp'] ?? '' );

    if ( ! $down )
      return NULL;

    if ( $down ['secret'] !== '' ) {

      $cookie = $_COOKIE [ padMaintenanceCookie () ] ?? '';

      if ( is_string ( $cookie ) and hash_equals ( padMaintenanceToken ( $down ), $cookie ) )
        return NULL;

      $asked = (string) ( $GLOBALS ['padPageAsked'] ?? '' );

      if ( $asked !== '' and hash_equals ( $down ['secret'], hash ( 'sha256', $asked ) ) ) {

        if ( ! headers_sent () ) {

          setcookie ( padMaintenanceCookie (), padMaintenanceToken ( $down ), [
            'expires'  => time () + 12 * 3600,
            'path'     => '/',
            'httponly' => TRUE,
            'samesite' => 'Lax',
            'secure'   => ( $_SERVER ['HTTPS'] ?? '' ) !== '' and ( $_SERVER ['HTTPS'] ?? '' ) !== 'off'
          ] );

          // The front page of the application, so the secret does not stay in the address
          // bar or the history: on $padHost once inits/host.php has made it, else relative.

          $home = isset ( $GLOBALS ['padHost'] ) ? $GLOBALS ['padHost'] . $GLOBALS ['padApp'] . '/' : './';

          http_response_code ( 302 );
          header ( 'Cache-Control: no-cache, no-store' );
          header ( "Location: $home" );

        }

        $stop = 302;
        include PAD . 'exits/exit.php';

      }

    }

    return $down;

  }

  function padMaintenanceRefuse ( $down ) {

    if ( $down ['retry'] > 0 and ! headers_sent () )
      header ( 'Retry-After: ' . $down ['retry'] );

    padRefuse ( 503, $down ['message'] !== '' ? $down ['message'] : 'Service Unavailable' );

  }

?>
