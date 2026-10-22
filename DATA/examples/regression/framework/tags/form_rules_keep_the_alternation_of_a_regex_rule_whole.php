<?php

  $altRules = json_encode ( array_column ( padValidateClient ( padFormRules ( 'f' ) ) ['size'] ['rules'], 1, 0 ) );

?>
