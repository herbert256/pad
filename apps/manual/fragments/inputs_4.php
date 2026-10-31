<?php

  // As if the form had been posted.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'guests' => '12' ];

  $errors = padValidate ( [
    'guests' => 'integer|between:1,8',
    'score'  => 'required' ] );

?>
