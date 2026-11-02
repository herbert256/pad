<?php

  $tripRules = padValidateClient ( [
    'address' => 'required_if:delivery,ship',
    'end'     => 'after:start' ] );

?>
