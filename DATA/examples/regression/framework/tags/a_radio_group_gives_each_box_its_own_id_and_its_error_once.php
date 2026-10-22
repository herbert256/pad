<?php

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'name' => 'Ann' ];

  padValidate ( [ 'size' => 'required', 'tags[]' => 'required' ] );

?>
