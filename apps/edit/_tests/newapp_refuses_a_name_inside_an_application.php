<?php

  // "New application" with a name under an application that exists is refused before
  // anything is made: pad new, which the call runs, takes such a name for a page of that
  // application - demo/admin made apps/demo/admin.php and admin.pad in demo, after which
  // the call failed and the two files stayed.

  $answer = 'made';

  try {
    ( function ( $body ) { return include APP . '_api/newapp.php'; } ) ( [ 'name' => 'demo/zz_edit_test' ] );
  } catch ( RuntimeException $e ) {
    $answer = $e->getMessage ();
  }

  $made = ( file_exists ( APPS . 'demo/zz_edit_test.php' ) or file_exists ( APPS . 'demo/zz_edit_test.pad' )
            or file_exists ( APPS . 'demo/zz_edit_test' ) ) ? 'yes' : 'no';

?>
