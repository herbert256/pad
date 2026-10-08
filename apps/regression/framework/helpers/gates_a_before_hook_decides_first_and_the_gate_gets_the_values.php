<?php

  // The gate gets the user and the values; a before hook is asked first, and its TRUE or
  // FALSE stands while its NULL leaves the answer to the gate. padAuthorize lets an allowed
  // user on. A name given twice keeps the second definition.

  $yes = fn ( $b ) => $b ? 'yes' : 'no';

  padGate ( 'edit-post', fn ( $user, $post ) => $user ['id'] == $post ['user_id'] );
  padGate ( 'move-post', fn ( $user, $post, $to ) => $to != 'trash' );
  padGate ( 'publish',   fn ( $user ) => FALSE );
  padGate ( 'publish',   fn ( $user ) => TRUE );

  $mine   = [ 'user_id' => 7 ];
  $theirs = [ 'user_id' => 8 ];

  padLogin ( [ 'id' => 7, 'role' => 'editor' ] );

  $editor = $yes ( padCan ( 'edit-post', $mine ) ) . ' ' . $yes ( padCan ( 'edit-post', $theirs ) )
          . ' ' . $yes ( padCannot ( 'edit-post', $theirs ) )
          . ' ' . $yes ( padCan ( 'move-post', $mine, 'archive' ) ) . ' ' . $yes ( padCan ( 'move-post', $mine, 'trash' ) )
          . ' ' . $yes ( padCan ( 'publish' ) ) . ' ' . $yes ( padAuthorize ( 'edit-post', $mine ) );

  padGateBefore ( fn ( $user, $ability ) => $user ['role'] == 'admin' ? TRUE : NULL );
  padGateBefore ( fn ( $user, $ability, ...$values ) => $ability == 'publish' ? FALSE : NULL );

  $hooked = $yes ( padCan ( 'edit-post', $theirs ) ) . ' ' . $yes ( padCan ( 'publish' ) );

  padLogin ( [ 'id' => 1, 'role' => 'admin' ] );

  $admin = $yes ( padCan ( 'edit-post', $theirs ) ) . ' ' . $yes ( padCan ( 'publish' ) );

  padLogout ();

  $guest = $yes ( padCan ( 'publish' ) );

?>
