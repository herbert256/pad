<?php

  $errors = padValidate (
    [ 'email'   => 'required|email',
      'message' => 'required|max:20' ],
    [ 'email'   => 'ann@',
      'message' => '' ] );

?>
