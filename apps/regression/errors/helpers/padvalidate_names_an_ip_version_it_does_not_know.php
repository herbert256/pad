<?php

  $errors = padValidate ( [ 'host' => 'required|ip:v5' ], [ 'host' => '10.0.0.1' ] );

?>
