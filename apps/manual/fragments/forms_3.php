<?php

  // As if the form had been posted with these values.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'email' => 'ann@', 'message' => 'Hello' ];

  $errors = padValidate ( [ 'email' => 'required|email', 'message' => 'required' ] );

?>
