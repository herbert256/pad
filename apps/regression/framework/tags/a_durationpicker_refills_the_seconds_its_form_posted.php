<?php

  // A post of form f: its length shows the seconds posted in place of value=, the length of
  // form g, which did not come back, shows value=, and a lap posted as text that is no number
  // of seconds is shown as it came - the number field marks it, and the page's rules say so.
  // The page's padValidate left a message for the length: it is tied to the field, and the
  // field's unit is said after it.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'length' => '5400', 'lap' => '1:30' ];

  padValidate ( [ 'length' => 'integer|max:3600' ] );

?>
