<?php

  $errors = padValidate ( [ 'zip' => 'required|regex:/^(\d{4}$/' ], [ 'zip' => '1234' ] );

?>
