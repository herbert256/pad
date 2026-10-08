<?php

  // The abilities the examples of {can} and {cannot} ask for. A guest reaches only a gate
  // whose user parameter takes NULL: read-post does, edit-post does not.

  padGate ( 'read-post', fn ( ?array $user, array $post ) => $post ['published'] or ( $user ['id'] ?? NULL ) === $post ['author'] );
  padGate ( 'edit-post', fn ( array $user, array $post ) => $user ['id'] === $post ['author'] );

?>
