<?php

  padGate ( 'edit-post', fn ( $user, $post ) => $user ['id'] == $post ['user_id'] );

  padLogin ( [ 'id' => 7 ] );

  padAuthorize ( 'edit-post', [ 'user_id' => 8 ] );

?>
