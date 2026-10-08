<?php

  // Nobody is logged in: a gate whose first parameter allows NULL is asked, any other is
  // not - and a before hook is skipped the same way.

  padGateBefore ( fn ( $user, $ability ) => TRUE );

  padGate ( 'view-post',   fn ( ?array $user, $post ) => $post ['public'] );
  padGate ( 'delete-post', fn ( $user, $post ) => TRUE );

  $post = [ 'public' => TRUE ];

?>
