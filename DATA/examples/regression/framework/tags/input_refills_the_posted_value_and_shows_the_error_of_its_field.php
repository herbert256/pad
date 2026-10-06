<?php

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'email' => 'x@', 'name' => 'Ann & Bob' ];

  padValidate ( [ 'email' => 'required|email', 'name' => 'required' ] );

?>
