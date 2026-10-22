<?php

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'comment', 'padFormAt' => 'list:3', 'text' => 'abc' ];

  padValidate ( [ 'text' => 'required|min:5' ] );

?>
