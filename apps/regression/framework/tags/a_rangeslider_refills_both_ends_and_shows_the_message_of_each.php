<?php

  // A post of form f: its two ends show what was posted for each - price_from and price_to -
  // in place of value=, the range of form g, which did not come back, shows value=. The
  // page's padValidate left a message for the upper end only: it stands after the frame,
  // tied to that end's range alone.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'price_from' => '250', 'price_to' => '480' ];

  padValidate ( [ 'price_from' => 'integer', 'price_to' => 'integer|max:450' ] );

?>
