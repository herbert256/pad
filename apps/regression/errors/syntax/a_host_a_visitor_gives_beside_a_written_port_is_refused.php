<?php

  // A host a visitor gives - ?h=127.0.0.1 - is his choice whatever the template writes
  // around it: with the port written, data='http://{$h}:9/...', no request value held the
  // host text 127.0.0.1:9 and the address was fetched.

  $_GET ['h'] = $h = '127.0.0.1';

?>
