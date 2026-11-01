<?php

  $errors = padValidate ( [ 'address' => 'required_if:delivery' ], [ 'delivery' => 'ship' ] );

?>
