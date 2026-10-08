<?php

  $signupRules = padValidateClient ( [
    'email' => 'required|email',
    'age'   => 'required|integer|min:18' ] );

?>
