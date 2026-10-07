<?php

  // A visitor who is not logged in gets the login - or, before there is any user, the
  // making of the first one: a form with a password field either way. The editor itself
  // sends them there.

  $login  = padCurl ( $padHost . 'edit/?login&padInclude' );
  $editor = padCurl ( $padHost . 'edit/' );

  $form   = fn ( $page ) => preg_match ( '/<form method="post">.*<input type="password" name="password"/s', $page ['data'] ) ? 'the login form' : 'no login form';

  $answer = 'login: ' . $login ['result'] . ' ' . $form ( $login ) . ' / editor: ' . $editor ['result'] . ' ' . $form ( $editor );

?>
