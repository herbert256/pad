<?php

  // address - A street, a postcode and a city on one line, 42 Baker Road, 02134 Lisbon.

  $street = padFakeStreet ();

  return $street . ', ' . padFake ( 'postcode' ) . ' ' . padFakeCity ();

?>
