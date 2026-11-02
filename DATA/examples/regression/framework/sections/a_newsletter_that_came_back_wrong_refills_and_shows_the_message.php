<?php

  // The newsletter's form came back with an address that is none: the page's own check finds
  // it, and the field shows what was typed and the message, tied to it for a screen reader.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'newsletter', 'email' => 'not "an" address' ];

  padValidate ( [ 'email' => 'required|email' ] );

?>
