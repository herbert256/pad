<?php

  $accountRules = padValidateClient ( [
    'password' => 'required|min:8|different:current_password' ] );

?>
