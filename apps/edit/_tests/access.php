<?php

  // The rule _guard.php asks - this machine and a login that holds - is asked by the editor's
  // two entry points too: a _guard.php the editor moved, like any file of the tree, left the
  // calls and the terminal open to every machine, with no login.

  $editUser  = '';
  $editStamp = '';

  $asks  = fn ( $file ) => str_contains ( file_get_contents ( APP . $file ), 'editLoggedIn ()' ) ? 'yes' : 'no';

  $shown = 'a web request without a login: ' . ( editLoggedIn ( FALSE ) ? 'let in' : 'refused' )
         . ' | api.php asks: ' . $asks ( 'api.php' ) . ' | index.php asks: ' . $asks ( 'index.php' );

?>
