<?php

  // The users: the list, adding and deleting one, and changing one's own password - which
  // gives this session the new stamp and ends the user's other sessions. This is the one
  // call that runs with the session still open (editApi), to write that stamp.

  global $editUser, $editStamp;

  $base = editStore ();

  switch ( editArg ( $body, 'op', 'list' ) ) {

    case 'list':
      $list = [];
      foreach ( editUsers ( $base ) as $name => $user )
        $list [] = [ 'name' => (string) $name, 'created' => $user ['created'] ?? '', 'self' => (string) $name === $editUser ];
      return $list;

    case 'add':
      editUserAdd ( $base, editArg ( $body, 'name' ), $body ['password'] ?? '' );
      return [];

    case 'delete':
      if ( editArg ( $body, 'name' ) === $editUser )
        editFail ( 'you cannot delete yourself' );
      editUserDelete ( $base, editArg ( $body, 'name' ) );
      return [];

    case 'password':
      if ( editUserCheck ( $base, $editUser, (string) ( $body ['current'] ?? '' ) ) === '' )
        editFail ( 'the current password is not right' );
      $editStamp = editUserPasswordSet ( $base, $editUser, $body ['password'] ?? '' );
      return [];

  }

  editFail ( 'users: list, add, delete or password' );

?>
