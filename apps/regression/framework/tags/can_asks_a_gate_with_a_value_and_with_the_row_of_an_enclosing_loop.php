<?php

  // A gate gets the user and the values {can} names: $post a page array, $posts inside
  // {posts} the row of that occurrence.

  padGate ( 'edit-post', fn ( $user, $post ) => $user ['id'] == $post ['user_id'] );

  padLogin ( [ 'id' => 7, 'name' => 'Ann' ] );

  $posts = [ [ 'title' => 'mine', 'user_id' => 7 ], [ 'title' => 'theirs', 'user_id' => 8 ] ];
  $post  = $posts [1];

?>
