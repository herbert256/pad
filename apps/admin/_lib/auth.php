<?php

  // Who may reach the console, and its users.
  //
  // adminAllowed    the machine rule of _guard.php: the command line always; a web request
  //                 only from this machine - the loopback address, nothing forwarded
  //                 (padLoopback) - naming this machine in its Host header, unless
  //                 $adminRemote opens it up. The Host check keeps out a page on another
  //                 site that has its own name resolve to 127.0.0.1 (DNS rebinding).
  // adminStore      DATA/admin/, where the users are kept
  // adminUsers      name => [ hash, stamp, created, login ]
  // adminUserAdd    a new user; answers the stamp its session carries
  // adminUserPassword  a new password for a user - and a new stamp, which ends its sessions
  // adminUserDelete removes a user - never the last one
  // adminUserCheck  the stamp of a user whose password matches, '' otherwise
  // adminUserValid  whether a session's user and stamp are still good
  //
  // A failure is a RuntimeException with a message for the visitor.

  function adminAllowed () {

    global $adminRemote, $adminHosts;

    if ( PHP_SAPI === 'cli' )
      return TRUE;

    $hosts = array_merge ( [ 'localhost', '127.0.0.1', '[::1]', '::1' ], (array) $adminHosts );
    $host  = strtolower ( preg_replace ( '/:\d+$/', '', trim ( (string) ( $_SERVER ['HTTP_HOST'] ?? '' ) ) ) );
    $named = in_array ( $host, array_map ( 'strtolower', $hosts ), TRUE );

    if ( $adminRemote )
      return ! $adminHosts or $named;

    return padLoopback () and $named;

  }

  function adminStore () {

    return DATA . 'admin/';

  }

  function adminUsers () {

    $data = json_decode ( (string) @file_get_contents ( adminStore () . 'users.json' ), TRUE );

    return is_array ( $data ['users'] ?? NULL ) ? $data ['users'] : [];

  }

  function adminUsersSave ( $users ) {

    ksort ( $users, SORT_STRING | SORT_FLAG_CASE );

    $dir = adminStore ();

    if ( ! is_dir ( $dir ) and ! @mkdir ( $dir, 0700, TRUE ) )
      throw new RuntimeException ( 'DATA/admin/ can not be made' );

    $file = $dir . 'users.json';
    $temp = $file . '.' . bin2hex ( random_bytes ( 4 ) );
    $json = json_encode ( [ 'users' => (object) $users ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";

    if ( @file_put_contents ( $temp, $json, LOCK_EX ) === FALSE or ! @rename ( $temp, $file ) ) {
      @unlink ( $temp );
      throw new RuntimeException ( 'DATA/admin/users.json can not be written' );
    }

    @chmod ( $file, 0600 );

  }

  function adminUserName ( $name ) {

    $name = trim ( (string) $name );

    if ( ! preg_match ( '/^[A-Za-z0-9_-]{1,40}$/D', $name ) )
      throw new RuntimeException ( 'a user name is 1 to 40 letters, digits, _ or -' );

    return $name;

  }

  function adminUserPasswordOk ( $password ) {

    if ( ! is_string ( $password ) or strlen ( $password ) < 8 )
      throw new RuntimeException ( 'a password has at least 8 characters' );

    if ( strlen ( $password ) > 200 )
      throw new RuntimeException ( 'a password has at most 200 characters' );

    return $password;

  }

  function adminUserAdd ( $name, $password ) {

    $name     = adminUserName ( $name );
    $password = adminUserPasswordOk ( $password );
    $users    = adminUsers ();

    if ( isset ( $users [$name] ) )
      throw new RuntimeException ( "there is a user named '$name' already" );

    $users [$name] = [ 'hash' => padHash ( $password ), 'stamp' => bin2hex ( random_bytes ( 8 ) ),
                       'created' => time (), 'login' => 0 ];

    adminUsersSave ( $users );

    return $users [$name] ['stamp'];

  }

  function adminUserPassword ( $name, $password ) {

    $password = adminUserPasswordOk ( $password );
    $users    = adminUsers ();

    if ( ! isset ( $users [$name] ) )
      throw new RuntimeException ( 'there is no such user' );

    $users [$name] ['hash']  = padHash ( $password );
    $users [$name] ['stamp'] = bin2hex ( random_bytes ( 8 ) );

    adminUsersSave ( $users );

    return $users [$name] ['stamp'];

  }

  function adminUserDelete ( $name ) {

    $users = adminUsers ();

    if ( ! isset ( $users [$name] ) )
      throw new RuntimeException ( 'there is no such user' );

    if ( count ( $users ) == 1 )
      throw new RuntimeException ( 'the last user stays: nobody could log in anymore' );

    unset ( $users [$name] );

    adminUsersSave ( $users );

  }

  // A name that is no user still costs a hash check, so the answer takes as long either way.

  function adminUserCheck ( $name, $password ) {

    static $none = NULL;

    $users = adminUsers ();
    $user  = $users [ (string) $name ] ?? NULL;
    $hash  = $user ['hash'] ?? ( $none ??= padHash ( bin2hex ( random_bytes ( 8 ) ) ) );

    if ( ! padHashCheck ( (string) $password, $hash ) or ! $user )
      return '';

    $users [$name] ['login'] = time ();

    try { adminUsersSave ( $users ); } catch ( RuntimeException $e ) { }

    return (string) $user ['stamp'];

  }

  function adminUserValid ( $name, $stamp ) {

    if ( ! is_string ( $name ) or ! is_string ( $stamp ) or $stamp === '' )
      return FALSE;

    $user = adminUsers () [$name] ?? NULL;

    return $user and hash_equals ( (string) $user ['stamp'], $stamp );

  }

?>
