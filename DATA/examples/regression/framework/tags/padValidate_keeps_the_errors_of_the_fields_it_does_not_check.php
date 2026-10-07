<?php

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'a' => '', 'b' => 'x', 'c' => '' ];

  padValidate ( [ 'a' => 'required', 'c' => 'required' ] );

  $second = count ( padValidate ( [ 'b' => 'min:3' ] ) );

  padValidate ( [ 'c' => 'max:5' ] );

?>
