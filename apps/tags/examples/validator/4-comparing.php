<?php

  $accountRules = padValidateClient ( [
    'password' => 'required|min:8|different:current_password',
    'bottles'  => 'integer|multiple_of:6|max:120' ] );

?>
