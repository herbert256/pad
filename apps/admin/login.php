<?php

  // The login - and, while DATA/admin/users.json holds nobody, the making of the first user,
  // which only this machine may do: there is no default password to guess. Five tries a
  // minute per address. The password is read as it was posted: a space at its end is part
  // of it.

  $title      = 'Log in';
  $loginSetup = adminUsers () ? 0 : 1;
  $loginError = '';
  $loginHint  = ( $loginSetup and PHP_SAPI !== 'cli' and ! padLoopback () ) ? 1 : 0;

  if ( adminUserValid ( $adminUser, $adminStamp ) )
    padRedirect ( 'index' );

  if ( padPosted ( 'login' ) ) {

    $loginKey = 'admin-login:' . ( $_SERVER ['REMOTE_ADDR'] ?? 'cli' );

    if ( ! padRateLimit ( $loginKey, 5, 60 ) ) {

      $loginError = 'Too many tries - wait ' . padRateLimitAvailableIn ( $loginKey ) . ' seconds.';

    } else {

      $loginName     = trim ( adminField ( 'name' ) );
      $loginPassword = adminField ( 'password' );
      $loginStamp    = '';

      try {

        if ( ! $loginSetup )
          $loginStamp = adminUserCheck ( $loginName, $loginPassword );
        elseif ( $loginHint )
          $loginError = 'The first user can only be made from this machine.';
        elseif ( $loginPassword !== adminField ( 'again' ) )
          $loginError = 'The two passwords are not the same.';
        else
          $loginStamp = adminUserAdd ( $loginName, $loginPassword );

      } catch ( RuntimeException $e ) {

        $loginError = ucfirst ( $e->getMessage () ) . '.';

      }

      if ( $loginStamp !== '' ) {

        padRateLimitClear ( $loginKey );
        padSessionRegenerate ();

        $adminUser  = $loginName;
        $adminStamp = $loginStamp;

        padRedirect ( 'index' );

      }

      if ( $loginError === '' )
        $loginError = 'The name or the password is not right.';

    }

  }

?>
