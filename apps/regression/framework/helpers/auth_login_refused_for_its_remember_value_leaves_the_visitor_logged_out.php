<?php

  // padLogin checks what it is asked to remember - TRUE or a number of seconds, an id the
  // cookie can carry, $padAuthRemember - before it logs in: a checkbox's 'on' was refused
  // after the session held the user, so the visitor got the error and was logged in all the
  // same. The error report shows the session: no login in it.

  if ( ! function_exists ( 'authRefusedFind' ) ) {
    function authRefusedFind ( $id ) { return NULL; }
  }

  $padAuthRemember = 'authRefusedFind';

  padLogin ( [ 'id' => 7, 'name' => 'Eve' ], 'on' );

?>
