<?php

  // The login - and, while DATA/edit/users.json holds nobody, the making of the first user,
  // which only this machine may do: there is no default password to guess. Five tries a
  // minute per address. The password is read as it was posted - padRequest and padValidate
  // trim, and a space at the end of a password is part of it.

  $loginSetup = editUsers ( editStore () ) ? 0 : 1;
  $loginError = '';
  $loginHint  = ( $loginSetup and PHP_SAPI !== 'cli' and ! padLoopback () ) ? 1 : 0;

  if ( editUserValid ( editStore (), $editUser, $editStamp ) )
    padRedirect ( 'index', editAppAsked () );

  if ( padPosted ( 'login' ) ) {

    $loginKey = 'edit-login:' . ( $_SERVER ['REMOTE_ADDR'] ?? 'cli' );

    if ( ! padRateLimit ( $loginKey, 5, 60 ) ) {

      $loginError = 'Too many tries - wait ' . padRateLimitAvailableIn ( $loginKey ) . ' seconds.';

    } else {

      $loginName     = trim ( (string) ( $_POST ['name'] ?? '' ) );
      $loginPassword = (string) ( $_POST ['password'] ?? '' );
      $loginStamp    = '';

      try {

        if ( ! $loginSetup )
          $loginStamp = editUserCheck ( editStore (), $loginName, $loginPassword );
        elseif ( $loginHint )
          $loginError = 'The first user can only be made from this machine.';
        elseif ( $loginPassword !== (string) ( $_POST ['again'] ?? '' ) )
          $loginError = 'The two passwords are not the same.';
        else
          $loginStamp = editUserAdd ( editStore (), $loginName, $loginPassword );

      } catch ( RuntimeException $e ) {

        $loginError = ucfirst ( $e->getMessage () ) . '.';

      }

      if ( $loginStamp !== '' ) {

        padRateLimitClear ( $loginKey );
        padSessionRegenerate ();

        $editUser  = $loginName;
        $editStamp = $loginStamp;

        padRedirect ( 'index', editAppAsked () );

      }

      if ( $loginError === '' )
        $loginError = 'The name or the password is not right.';

    }

  }

?>
