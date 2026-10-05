<?php

  // As if a form had come back with a mistake in it: the page that took the post keeps the
  // input - and would then send the browser back to the form with padBack ().

  $_POST = [ 'email' => 'ann@example', 'name' => 'Ann', 'password' => 'secret' ];

  padFlashInput ();

  // The form's page reads it on the next request - and this one already.

  $email    = padOld ( 'email' );
  $name     = padOld ( 'name' );
  $password = padOld ( 'password', '(never kept)' );
  $city     = padOld ( 'city', 'Amsterdam' );

?>
