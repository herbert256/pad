<?php

  // The newsletter's form came back with a good address: padPosted ( 'newsletter' ) holds,
  // and the section says thanks in the form's place.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'newsletter', 'email' => 'ann@example.com' ];

  padValidate ( [ 'email' => 'required|email' ] );

  $posted = padPosted ( 'newsletter' ) ? 'posted' : 'not posted';

?>
