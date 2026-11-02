<?php

  $names = [ 'progress', 'rating', 'placeholder', 'jsonview' ];
  $ok = TRUE;
  foreach ( $names as $name )
    $ok = $ok && padTypeTagCheck ( 'widget', $name ) === 'widget' && ! padTagBuiltIn ( $name ) && ! is_file ( PAD . "tags/_lib/$name.php" );
  $answer = $ok ? 'four renderers belong to widgets' : 'FAIL';

?>
