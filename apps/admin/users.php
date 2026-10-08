<?php

  // The users of the console, kept in DATA/admin/users.json: who there is, when each logged
  // in last; adding one, a new password, removing one. A new password - one's own included
  // - ends every session of that user but the one that set it; the last user stays.

  $title = 'Users';

  if ( adminPost () ) {

    $action = adminField ( 'action' );
    $name   = adminField ( 'name' );

    try {

      if ( $action == 'add' ) {
        if ( adminField ( 'password' ) !== adminField ( 'again' ) )
          throw new RuntimeException ( 'the two passwords are not the same' );
        adminUserAdd ( $name, adminField ( 'password' ) );
        adminDone ( "$name can log in now.", 'users' );
      }

      if ( $action == 'password' ) {
        if ( adminField ( 'password' ) !== adminField ( 'again' ) )
          throw new RuntimeException ( 'the two passwords are not the same' );
        $stamp = adminUserPassword ( $name, adminField ( 'password' ) );
        if ( $name === $adminUser )
          $adminStamp = $stamp;
        adminDone ( "The password of $name is changed.", 'users' );
      }

      if ( $action == 'delete' ) {
        if ( $name === $adminUser )
          throw new RuntimeException ( 'you can not remove yourself' );
        adminUserDelete ( $name );
        adminDone ( "$name is removed.", 'users' );
      }

    } catch ( RuntimeException $e ) {

      adminDone ( ucfirst ( $e->getMessage () ) . '.', 'users', [], 'error' );

    }

  }

  $userRows = [];

  foreach ( adminUsers () as $name => $one )
    $userRows [] = [ 'name' => $name, 'created' => adminWhen ( $one ['created'] ?? 0 ),
                     'login' => ( $one ['login'] ?? 0 ) ? adminAgo ( $one ['login'] ) : 'never',
                     'self' => $name === $adminUser ? 1 : 0 ];

  $userCount = count ( $userRows );

?>
